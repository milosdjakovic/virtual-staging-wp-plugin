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
require_once plugin_dir_path(__FILE__) . 'includes/class-vsai-template-renderer.php';

if (file_exists($router_file) && file_exists($handlers_file) && file_exists($env_loader_file) && file_exists($authorizer_file) && file_exists($api_client_file)) {
  require_once $router_file;
  require_once $handlers_file;
  require_once $env_loader_file;
  require_once $authorizer_file;
  require_once $api_client_file;

  // Initialize the router
  function vsai_init_router()
  {
    $env_loader = new VSAI_Env_Loader();
    $authorizer = new VSAI_Authorizer($env_loader);
    $api_client = new VSAI_API_Client($env_loader);
    $handlers = new VSAI_Handlers($api_client);
    $router = new VSAI_Router($handlers, $authorizer);
    $router->register_routes();
  }

  add_action('rest_api_init', 'vsai_init_router');

  $template_renderer = new VSAI_Template_Renderer();
  $template_renderer->register_template('upload_form', plugin_dir_path(__FILE__) . 'templates/upload-form.php');
  $template_renderer->register_template('main_page', plugin_dir_path(__FILE__) . 'templates/main-page.php');
  // Enqueue styles
  add_action('wp_enqueue_scripts', array($template_renderer, 'enqueue_template_styles'));

} else {
  error_log('Virtual Staging API: Required files are missing.');
}
