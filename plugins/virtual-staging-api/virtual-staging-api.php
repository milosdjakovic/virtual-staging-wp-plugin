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

// Initialize the plugin
function vsai_initialize_plugin()
{
  $plugin = new VSAI_Plugin();
}
add_action('plugins_loaded', 'vsai_initialize_plugin');
