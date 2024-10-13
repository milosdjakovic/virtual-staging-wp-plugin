<?php

namespace VirtualStagingAdapter\Service;

use VirtualStagingAdapter\Config\ConfigInterface;

class RedirectService
{
  private $config;

  public function __construct(ConfigInterface $config)
  {
    $this->config = $config;
  }

  public function redirect($path, $at_value)
  {
    $redirectUrl = add_query_arg('at', $at_value, home_url($path));

    wp_redirect($redirectUrl);
    exit;
  }
}
