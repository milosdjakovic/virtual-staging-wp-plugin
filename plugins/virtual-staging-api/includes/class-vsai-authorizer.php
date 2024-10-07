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

  public function check_authorization()
  {
    $dev_mode = $this->env_loader->get_env('DEV_MODE', 'false');

    if ($dev_mode === 'true') {
      return true;
    }

    // Check for valid WordPress cookie
    return $this->is_user_logged_in();
  }

  private function is_user_logged_in()
  {
    // This function will check for a valid WordPress user session
    return is_user_logged_in();
  }
}
