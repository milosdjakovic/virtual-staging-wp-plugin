<?php
/*
Plugin Name: Virtual Staging API Form Adapter
Description: Redirects to a specified path after WPForms submission
Version: 2.0
Author: Your Name
*/

if (!defined('ABSPATH')) {
  exit;
}

// Include the constants file
require_once __DIR__ . '/constants.php';

// Autoloader function
spl_autoload_register(function ($class) {
  $prefix = 'VirtualStagingAdapter\\';
  $base_dir = __DIR__ . '/includes/';
  $len = strlen($prefix);
  if (strncmp($prefix, $class, $len) !== 0) {
    return;
  }
  $relative_class = substr($class, $len);
  $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
  if (file_exists($file)) {
    require $file;
  }
});

use VirtualStagingAdapter\Plugin;

function run_virtual_staging_adapter()
{
  $plugin = new Plugin();
  $plugin->run();
}

run_virtual_staging_adapter();
