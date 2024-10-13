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
      $redirectUrl = add_query_arg('at', urlencode($token), home_url($path));
      wp_redirect($redirectUrl);
      exit;
    } else {
      // Handle the error case, maybe redirect to an error page
      wp_redirect(home_url('/error-page'));
      exit;
    }
  }
}
