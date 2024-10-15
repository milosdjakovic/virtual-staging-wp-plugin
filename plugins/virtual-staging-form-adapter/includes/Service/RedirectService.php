<?php

namespace VirtualStagingAdapter\Service;

use VirtualStagingAdapter\Config\ConfigInterface;

class RedirectService
{
  private $config;
  private $tokenService;

  public function __construct(ConfigInterface $config, TokenService $tokenService)
  {
    $this->config = $config;
    $this->tokenService = $tokenService;
  }

  public function redirect($path, $limit)
  {
    $token = $this->tokenService->generateToken($limit);

    if ($token) {
      $redirectUrl = $this->getRedirectUrl($path, $token);
      wp_redirect($redirectUrl);
      exit;
    } else {
      $errorPath = $this->config->get('vsa_error_path', '/error');
      wp_redirect(home_url($errorPath));
      exit;
    }
  }

  public function getRedirectUrl($path, $token)
  {
    return add_query_arg('at', urlencode($token), home_url($path));
  }
}
