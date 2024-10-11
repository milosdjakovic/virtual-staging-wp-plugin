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



// === DEBUG START ===
// if (!defined('WP_DEBUG')) {
//   define('WP_DEBUG', true);
// }
// if (!defined('WP_DEBUG_LOG')) {
//   define('WP_DEBUG_LOG', true);
// }
// if (!defined('WP_DEBUG_DISPLAY')) {
//   define('WP_DEBUG_DISPLAY', false);
// }

// // Ensure error logging is enabled
// ini_set('log_errors', 1);
// ini_set('error_log', '/var/www/html/wp-content/debug.log');
// === DEBUG END ===


// Define plugin constants
define('VSAI_PLUGIN_FILE', __FILE__);
define('VSAI_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('VSAI_PLUGIN_URL', plugin_dir_url(__FILE__));

// Load the main plugin class
require_once VSAI_PLUGIN_DIR . 'includes/class-vsai-plugin.php';

// Global variable for the plugin instance
$vsai_plugin_instance = null;

// Initialize the plugin
function vsai_initialize_plugin()
{
  global $vsai_plugin_instance;
  if (null === $vsai_plugin_instance) {
    $vsai_plugin_instance = new VSAI_Plugin();
  }
}
add_action('plugins_loaded', 'vsai_initialize_plugin');

// Activation hook
register_activation_hook(__FILE__, 'vsai_activate_plugin');

function vsai_activate_plugin()
{
  global $vsai_plugin_instance;

  // If the plugin hasn't been initialized yet, do so now
  if (null === $vsai_plugin_instance) {
    vsai_initialize_plugin();
  }

  // Call the activate_plugin method to set up the token table
  $vsai_plugin_instance->activate_plugin();

  // Log activation
  error_log('VSAI Plugin activated and token table initialized');
}
