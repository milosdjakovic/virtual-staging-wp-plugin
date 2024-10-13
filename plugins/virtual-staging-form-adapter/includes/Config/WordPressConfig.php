<?php

namespace VirtualStagingAdapter\Config;

class WordPressConfig implements ConfigInterface
{
  public function get($key)
  {
    return get_option($key);
  }

  public function set($key, $value)
  {
    update_option($key, $value);
  }
}
