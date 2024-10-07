<?php
// Exit if accessed directly
if (!defined('ABSPATH'))
  exit;

class VSAI_Env_Loader
{
  private $env_vars = [];

  public function __construct()
  {
    $this->load_env_file();
  }

  private function load_env_file()
  {
    $env_file = plugin_dir_path(dirname(__FILE__)) . '.env';
    if (file_exists($env_file)) {
      $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
      foreach ($lines as $line) {
        if (strpos($line, '=') !== false) {
          list($key, $value) = explode('=', $line, 2);
          $key = trim($key);
          $value = trim($value);
          $this->env_vars[$key] = $value;
        }
      }
    }
  }

  public function get_env($key, $default = null)
  {
    return isset($this->env_vars[$key]) ? $this->env_vars[$key] : $default;
  }

  public function get_all_env()
  {
    return [
      'VIRTUAL_STAGING_API_URL' => $this->get_env('VIRTUAL_STAGING_API_URL'),
      'VIRTUAL_STAGING_API_KEY' => $this->get_env('VIRTUAL_STAGING_API_KEY'),
      'DEV_MODE' => $this->get_env('DEV_MODE')
    ];
  }
}
