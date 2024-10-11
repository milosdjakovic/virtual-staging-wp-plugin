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
  private $token_handler;

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
    require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-vsai-token-handler.php';
  }

  private function initialize_components()
  {
    $this->env_loader = new VSAI_Env_Loader();
    $this->authorizer = new VSAI_Authorizer($this->env_loader);
    $this->api_client = new VSAI_API_Client($this->env_loader);
    $this->token_handler = new VSAI_Token_Handler();
    $this->handlers = new VSAI_Handlers($this->api_client, $this->token_handler);
    $this->router = new VSAI_Router($this->handlers, $this->authorizer);

    $plugin_url = plugin_dir_url(dirname(__FILE__));
    $this->template_renderer = new VSAI_Template_Renderer($plugin_url, $this->api_client);
  }

  private function setup_hooks()
  {
    add_action('init', array($this, 'init'));
    add_shortcode('vsai_template', array($this, 'vsai_template_shortcode'));
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

  public function vsai_template_shortcode($atts)
  {
    $atts = shortcode_atts(
      array(
        'type' => 'main',
        'data' => '{}',
        'next_page_url' => '',
      ),
      $atts,
      'vsai_template'
    );

    $type = $atts['type'];
    $data = json_decode($atts['data'], true);
    $next_page_url = esc_url($atts['next_page_url']);

    if (!in_array($type, ['main', 'upload'])) {
      return "Invalid template type specified.";
    }

    $template_data = array(
      'next_page_url' => $next_page_url,
    );

    if (is_array($data)) {
      $template_data = array_merge($template_data, $data);
    }

    return $this->template_renderer->render_template($type, $template_data);
  }

  public function delete_uploaded_image($file_path)
  {
    if (file_exists($file_path)) {
      wp_delete_file($file_path);
    }
  }

  public function activate_plugin()
  {
    if (!$this->token_handler) {
      $this->initialize_components();
    }
    $this->token_handler->initialize();
  }
}
