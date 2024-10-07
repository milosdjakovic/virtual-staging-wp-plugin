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

  public function get_posts_handler($request)
  {
    $response = wp_remote_get('https://jsonplaceholder.typicode.com/posts');

    if (is_wp_error($response)) {
      return new WP_Error('api_error', 'Failed to fetch posts', array('status' => 500));
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
      return new WP_Error('json_error', 'Failed to parse JSON', array('status' => 500));
    }

    return $data;
  }

  // Add more handler methods here as needed
}
