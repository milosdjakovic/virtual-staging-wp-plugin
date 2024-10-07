<?php
// Exit if accessed directly
if (!defined('ABSPATH'))
  exit;

class VSAI_API_Client
{
  private $api_base_url;
  private $api_key;

  public function __construct($env_loader)
  {
    $this->api_base_url = $env_loader->get_env('VIRTUAL_STAGING_API_URL');
    $this->api_key = $env_loader->get_env('VIRTUAL_STAGING_API_KEY');
  }

  public function request($endpoint, $method = 'GET', $body = null)
  {
    if (!$this->api_base_url || !$this->api_key) {
      return new WP_Error('missing_config', 'API URL or API Key is missing', array('status' => 500));
    }

    $full_url = rtrim($this->api_base_url, '/') . '/' . ltrim($endpoint, '/');

    $args = array(
      'method' => $method,
      'headers' => array(
        'Authorization' => 'Api-Key ' . $this->api_key,
        'Content-Type' => 'application/json',
      ),
    );

    if ($body && in_array($method, ['POST', 'PUT', 'PATCH'])) {
      $args['body'] = json_encode($body);
    }

    $response = wp_remote_request($full_url, $args);

    if (is_wp_error($response)) {
      return new WP_Error('api_error', 'Failed to make API request: ' . $response->get_error_message(), array('status' => 500));
    }

    $status_code = wp_remote_retrieve_response_code($response);
    $body = wp_remote_retrieve_body($response);

    $data = json_decode($body, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
      return new WP_Error('json_error', 'Failed to parse JSON: ' . json_last_error_msg(), array('status' => 500, 'body' => $body));
    }

    return $data;
  }
}
