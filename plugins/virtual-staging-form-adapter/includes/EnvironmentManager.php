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

  public function getEnvFilePath()
  {
    return WP_PLUGIN_DIR . '/virtual-staging-api/.env';
  }

  private function loadEnv()
  {
    $envFile = $this->getEnvFilePath();

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

  public function getLocale()
  {
    return $this->get('LOCALE', 'en.json');
  }

  public function get($key, $default = null)
  {
    return isset($this->env[$key]) ? $this->env[$key] : $default;
  }
}
