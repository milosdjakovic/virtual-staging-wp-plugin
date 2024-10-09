<?php
/*
Plugin Name: Virtual Staging API
Description: WordPress plugin to interact with Virtual Staging AI API
Version: 1.0
Author: Your Name
*/

// Exit if accessed directly
if (!defined('ABSPATH'))
  exit;

// Include necessary files
$router_file = plugin_dir_path(__FILE__) . 'includes/class-vsai-router.php';
$handlers_file = plugin_dir_path(__FILE__) . 'includes/class-vsai-handlers.php';
$env_loader_file = plugin_dir_path(__FILE__) . 'includes/class-vsai-env-loader.php';
$authorizer_file = plugin_dir_path(__FILE__) . 'includes/class-vsai-authorizer.php';
$api_client_file = plugin_dir_path(__FILE__) . 'includes/class-vsai-api-client.php';
$template_renderer_file = plugin_dir_path(__FILE__) . 'includes/class-vsai-template-renderer.php'; // Add this line

// Check if all required files exist
if (
  file_exists($router_file) && file_exists($handlers_file) && file_exists($env_loader_file) &&
  file_exists($authorizer_file) && file_exists($api_client_file) && file_exists($template_renderer_file)
) {
  require_once $router_file;
  require_once $handlers_file;
  require_once $env_loader_file;
  require_once $authorizer_file;
  require_once $api_client_file;
  require_once $template_renderer_file; // Add this line

  // Initialize the plugin
  function vsai_init()
  {
    $env_loader = new VSAI_Env_Loader();
    $authorizer = new VSAI_Authorizer($env_loader);
    $api_client = new VSAI_API_Client($env_loader);
    $handlers = new VSAI_Handlers($api_client);
    $router = new VSAI_Router($handlers, $authorizer);
    $router->register_routes();

    // Initialize template renderer
    $plugin_url = plugin_dir_url(__FILE__);
    $template_renderer = new VSAI_Template_Renderer($plugin_url);

    // Register templates
    $template_renderer->register_template('main', plugin_dir_path(__FILE__) . 'templates/main/index.html');
    $template_renderer->register_template('upload', plugin_dir_path(__FILE__) . 'templates/upload/index.html');
  }

  add_action('init', 'vsai_init');

  // Enqueue block editor assets
  function vsai_enqueue_block_editor_assets()
  {
    wp_enqueue_script(
      'vsai-template-block',
      plugins_url('assets/js/vsai-template-block.js', __FILE__),
      array('wp-blocks', 'wp-element', 'wp-components'),
      filemtime(plugin_dir_path(__FILE__) . 'assets/js/vsai-template-block.js')
    );
  }
  add_action('enqueue_block_editor_assets', 'vsai_enqueue_block_editor_assets');

  // Register the block
  function vsai_register_block()
  {
    register_block_type('vsai/template-block', array(
      'render_callback' => 'vsai_render_template_block'
    ));
  }
  add_action('init', 'vsai_register_block');

  // Render callback for the block
  function vsai_render_template_block($attributes)
  {
    $name = isset($attributes['name']) ? $attributes['name'] : '';
    $data = isset($attributes['data']) ? $attributes['data'] : '{}';

    // Use the existing shortcode render function
    $shortcode = sprintf('[vsai_template name="%s" data=\'%s\']', esc_attr($name), esc_attr($data));
    return do_shortcode($shortcode);
  }

} else {
  error_log('Virtual Staging API: Required files are missing.');
}
