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

if (file_exists($router_file) && file_exists($handlers_file) && file_exists($env_loader_file)) {
  require_once $router_file;
  require_once $handlers_file;
  require_once $env_loader_file;

  // Initialize the router
  function vsai_init_router()
  {
    $env_loader = new VSAI_Env_Loader();
    $handlers = new VSAI_Handlers($env_loader);
    $router = new VSAI_Router($handlers);
    $router->register_routes();
  }

  add_action('rest_api_init', 'vsai_init_router');
} else {
  error_log('Virtual Staging API: Required files are missing.');
}
