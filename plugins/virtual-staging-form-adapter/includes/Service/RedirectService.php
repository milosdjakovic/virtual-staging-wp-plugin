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
    wp_redirect(home_url($path));
    exit;
  }
}
