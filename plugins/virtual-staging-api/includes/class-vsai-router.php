<?php
// Exit if accessed directly
if (!defined('ABSPATH'))
  exit;

class VSAI_Router
{
  private $handlers;

  public function __construct($handlers)
  {
    $this->handlers = $handlers;
  }

  public function register_routes()
  {
    register_rest_route('vsai/v1', '/ping', array(
      'methods' => 'GET',
      'callback' => array($this->handlers, 'ping_handler'),
      'permission_callback' => '__return_true'
    ));

    register_rest_route('vsai/v1', '/get-env', array(
      'methods' => 'GET',
      'callback' => array($this->handlers, 'get_env_handler'),
      'permission_callback' => '__return_true'
    ));

    // Add more routes here as needed
  }
}
