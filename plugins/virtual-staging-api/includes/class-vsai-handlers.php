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

  public function options_handler($request)
  {
    $api_base_url = $this->env_loader->get_env('VIRTUAL_STAGING_API_URL');
    $api_key = $this->env_loader->get_env('VIRTUAL_STAGING_API_KEY');

    if (!$api_base_url || !$api_key) {
      return new WP_Error('missing_config', 'API URL or API Key is missing', array('status' => 500));
    }

    // Remove trailing slash if present and append 'options'
    $full_url = rtrim($api_base_url, '/') . '/options';

    $response = wp_remote_get($full_url, array(
      'headers' => array(
        'Authorization' => 'Api-Key ' . $api_key,
      ),
    ));

    if (is_wp_error($response)) {
      return new WP_Error('api_error', 'Failed to fetch options: ' . $response->get_error_message(), array('status' => 500));
    }

    $status_code = wp_remote_retrieve_response_code($response);
    $body = wp_remote_retrieve_body($response);

    if ($status_code !== 200) {
      return new WP_Error('api_error', 'API returned non-200 status code: ' . $status_code, array('status' => $status_code, 'body' => $body));
    }

    $data = json_decode($body, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
      return new WP_Error('json_error', 'Failed to parse JSON: ' . json_last_error_msg(), array('status' => 500, 'body' => $body));
    }

    return $data;
  }
  // Add more handler methods here as needed
}
