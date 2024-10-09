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
