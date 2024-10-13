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

  public function redirect($path, $token_url)
  {
    $redirectUrl = add_query_arg('at', urlencode($token_url), home_url($path));

    wp_redirect($redirectUrl);
    exit;
  }
}
