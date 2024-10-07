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

  public function create_render_handler($request)
  {
    $params = $request->get_json_params();

    // Validate required parameters
    $required_params = ['image_url', 'room_type', 'style'];
    foreach ($required_params as $param) {
      if (!isset($params[$param])) {
        return new WP_Error('missing_parameter', "Missing required parameter: $param", array('status' => 400));
      }
    }

    // Set default value for wait_for_completion if not provided
    $params['wait_for_completion'] = isset($params['wait_for_completion']) ? $params['wait_for_completion'] : false;

    // Call the API client method
    return $this->api_client->request('render/create', 'POST', $params);
  }

  public function get_render_status_handler($request)
  {
    $render_id = $request->get_param('render_id');

    if (!$render_id) {
      return new WP_Error('missing_parameter', "Missing required parameter: render_id", array('status' => 400));
    }

    // Call the API client method
    return $this->api_client->request("render?render_id=$render_id", 'GET');
  }

  public function create_render_variation_handler($request)
  {
    $render_id = $request->get_param('render_id');
    $params = $request->get_json_params();

    if (!$render_id) {
      return new WP_Error('missing_parameter', "Missing required parameter: render_id", array('status' => 400));
    }

    // Validate required parameters
    $required_params = ['style', 'roomType'];
    foreach ($required_params as $param) {
      if (!isset($params[$param])) {
        return new WP_Error('missing_parameter', "Missing required parameter: $param", array('status' => 400));
      }
    }

    // Set default value for wait_for_completion if not provided
    $params['wait_for_completion'] = isset($params['wait_for_completion']) ? $params['wait_for_completion'] : false;

    // Call the API client method
    return $this->api_client->request("render/create-variation?render_id=$render_id", 'POST', $params);
  }



  // Add more handler methods here as needed
}
