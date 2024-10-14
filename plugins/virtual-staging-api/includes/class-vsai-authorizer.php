<?php
// Exit if accessed directly
if (!defined('ABSPATH'))
  exit;

class VSAI_Authorizer
{
  private $env_loader;

  public function __construct($env_loader)
  {
    $this->env_loader = $env_loader;
  }

  public function check_authorization($request)
  {
    $dev_mode = $this->env_loader->get_env('DEV_MODE', 'false');

    if ($dev_mode === 'true') {
      return true;
    }

    // Check for valid WordPress nonce
    $nonce = $request->get_header('X-WP-Nonce');
    if (!wp_verify_nonce($nonce, 'wp_rest')) {
      return new WP_Error('rest_forbidden', 'Invalid nonce', array('status' => 403));
    }

    return true;
  }
}
