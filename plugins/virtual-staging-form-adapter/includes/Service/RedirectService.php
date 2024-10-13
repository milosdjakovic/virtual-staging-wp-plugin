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

  public function redirect($path)
  {
    // Add the 'at' query parameter with value 'test'
    $redirectUrl = add_query_arg('at', 'test', home_url($path));

    wp_redirect($redirectUrl);
    exit;
  }
}
