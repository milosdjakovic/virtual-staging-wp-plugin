<?php
// Exit if accessed directly
if (!defined('ABSPATH'))
  exit;

class VSAI_Language_Loader
{
  private $locale_data = [];

  public function __construct($locale)
  {
    $this->load_locale_file($locale);
  }

  private function load_locale_file($locale)
  {
    $locale_file = plugin_dir_path(dirname(__FILE__)) . 'locale/' . $locale;
    if (file_exists($locale_file)) {
      $this->locale_data = json_decode(file_get_contents($locale_file), true);
    }
  }

  public function get_translation($key, $default = '')
  {
    $keys = explode('.', $key);
    $data = $this->locale_data;

    foreach ($keys as $k) {
      if (isset($data[$k])) {
        $data = $data[$k];
      } else {
        return $default;
      }
    }

    return $data;
  }

  public function get_all_translations()
  {
    return $this->locale_data;
  }
}

