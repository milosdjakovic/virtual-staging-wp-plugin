<?php

namespace VirtualStagingAdapter;

class EnvironmentManager
{
  private static $instance = null;
  private $env = [];

  private function __construct()
  {
    $this->loadEnv();
  }

  public static function getInstance()
  {
    if (self::$instance === null) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  private function loadEnv()
  {
    $pluginRoot = dirname(__FILE__); // This will point to the includes directory
    $envFile = dirname($pluginRoot) . '/.env'; // Go up one level to the plugin root

    if (file_exists($envFile)) {
      $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
      foreach ($lines as $line) {
        if (strpos($line, '=') !== false) {
          list($key, $value) = explode('=', $line, 2);
          $key = trim($key);
          $value = trim($value);
          $this->env[$key] = $value;
        }
      }
    }

    // Set DEV_MODE based on .env file or default to false
    $this->env['DEV_MODE'] = isset($this->env['DEV_MODE']) ? filter_var($this->env['DEV_MODE'], FILTER_VALIDATE_BOOLEAN) : false;
  }

  public function isDev()
  {
    return $this->env['DEV_MODE'];
  }

  public function get($key, $default = null)
  {
    return isset($this->env[$key]) ? $this->env[$key] : $default;
  }
}
