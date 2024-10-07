<?php
// Exit if accessed directly
if (!defined('ABSPATH'))
  exit;

class VSAI_Router
{
  private $handlers;
  private $authorizer;

  public function __construct($handlers, $authorizer)
  {
    $this->handlers = $handlers;
    $this->authorizer = $authorizer;
  }

  public function register_routes()
  {
    register_rest_route('vsai/v1', '/hello-world', array(
      'methods' => 'GET',
      'callback' => array($this->handlers, 'hello_world_handler'),
      'permission_callback' => array($this->authorizer, 'check_authorization')
    ));

    register_rest_route('vsai/v1', '/get-env', array(
      'methods' => 'GET',
      'callback' => array($this->handlers, 'get_env_handler'),
      'permission_callback' => array($this->authorizer, 'check_authorization')
    ));

    register_rest_route('vsai/v1', '/get-posts', array(
      'methods' => 'GET',
      'callback' => array($this->handlers, 'get_posts_handler'),
      'permission_callback' => array($this->authorizer, 'check_authorization')
    ));

    register_rest_route('vsai/v1', '/options', array(
      'methods' => 'GET',
      'callback' => array($this->handlers, 'options_handler'),
      'permission_callback' => array($this->authorizer, 'check_authorization')
    ));

    register_rest_route('vsai/v1', '/ping', array(
      'methods' => 'GET',
      'callback' => array($this->handlers, 'ping_handler'),
      'permission_callback' => array($this->authorizer, 'check_authorization')
    ));

    // Add more routes here as needed
  }
}
