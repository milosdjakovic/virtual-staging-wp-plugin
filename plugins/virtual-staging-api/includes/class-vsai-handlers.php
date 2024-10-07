<?php
// Exit if accessed directly
if (!defined('ABSPATH'))
  exit;

class VSAI_Handlers
{
  private $api_client;

  public function __construct($api_client)
  {
    $this->api_client = $api_client;
  }

  public function hello_world_handler($request)
  {
    return array(
      'message' => 'Hello World'
    );
  }

  public function get_env_handler($request)
  {
    // This method should be removed or handled differently,
    // as we no longer have direct access to env_loader here
    return new WP_Error('not_implemented', 'This endpoint is no longer available', array('status' => 501));
  }

  public function get_posts_handler($request)
  {
    // Consider moving this to the VSAI_API_Client if it's related to your API
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

  public function options_handler($request)
  {
    return $this->api_client->request('options');
  }

  public function ping_handler($request)
  {
    return $this->api_client->request('ping');
  }

  // Add more handler methods here as needed
}
