<?php
// Exit if accessed directly
if (!defined('ABSPATH'))
  exit;

class VSAI_Handlers
{
  private $env_loader;

  public function __construct($env_loader)
  {
    $this->env_loader = $env_loader;
  }

  public function ping_handler($request)
  {
    return array(
      'message' => 'Hello World'
    );
  }

  public function get_env_handler($request)
  {
    return $this->env_loader->get_all_env();
  }

  // Add more handler methods here as needed
}
