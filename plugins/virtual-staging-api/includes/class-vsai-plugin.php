<?php
// Exit if accessed directly
if (!defined('ABSPATH'))
  exit;

class VSAI_Plugin
{
  private $env_loader;
  private $authorizer;
  private $api_client;
  private $handlers;
  private $router;
  private $template_renderer;

  public function __construct()
  {
    $this->load_dependencies();
    $this->initialize_components();
    $this->setup_hooks();
  }

  private function load_dependencies()
  {
    require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-vsai-env-loader.php';
    require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-vsai-authorizer.php';
    require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-vsai-api-client.php';
    require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-vsai-handlers.php';
    require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-vsai-router.php';
    require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-vsai-template-renderer.php';
  }

  private function initialize_components()
  {
    $this->env_loader = new VSAI_Env_Loader();
    $this->authorizer = new VSAI_Authorizer($this->env_loader);
    $this->api_client = new VSAI_API_Client($this->env_loader);
    $this->handlers = new VSAI_Handlers($this->api_client);
    $this->router = new VSAI_Router($this->handlers, $this->authorizer);

    $plugin_url = plugin_dir_url(dirname(__FILE__));
    $this->template_renderer = new VSAI_Template_Renderer($plugin_url);
  }

  private function setup_hooks()
  {
    add_action('init', array($this, 'init'));
    add_action('enqueue_block_editor_assets', array($this, 'enqueue_block_editor_assets'));
    add_action('init', array($this, 'register_block'));
    add_shortcode('vsai_test_form', array($this, 'test_form_shortcode'));
    add_action('vsai_delete_uploaded_image', array($this, 'delete_uploaded_image'));
  }

  public function init()
  {
    $this->router->register_routes();
    $this->register_templates();
  }

  private function register_templates()
  {
    $this->template_renderer->register_template('main', plugin_dir_path(dirname(__FILE__)) . 'templates/main/index.php');
    $this->template_renderer->register_template('upload', plugin_dir_path(dirname(__FILE__)) . 'templates/upload/index.php');
    $this->template_renderer->register_template('test', plugin_dir_path(dirname(__FILE__)) . 'templates/test/index.php');
  }

  public function enqueue_block_editor_assets()
  {
    wp_enqueue_script(
      'vsai-template-block',
      plugins_url('assets/js/vsai-template-block.js', dirname(__FILE__)),
      array('wp-blocks', 'wp-element', 'wp-components'),
      filemtime(plugin_dir_path(dirname(__FILE__)) . 'assets/js/vsai-template-block.js')
    );
  }

  public function register_block()
  {
    register_block_type('vsai/template-block', array(
      'render_callback' => array($this, 'render_template_block')
    ));
  }

  public function render_template_block($attributes)
  {
    $name = isset($attributes['name']) ? $attributes['name'] : '';
    $data = isset($attributes['data']) ? $attributes['data'] : '{}';

    return $this->template_renderer->render_template($name, json_decode($data, true));
  }

  public function test_form_shortcode($atts)
  {
    return $this->template_renderer->render_template('test');
  }

  public function delete_uploaded_image($file_path)
  {
    if (file_exists($file_path)) {
      wp_delete_file($file_path);
    }
  }
}
