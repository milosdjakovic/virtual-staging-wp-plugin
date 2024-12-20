<?php
// Exit if accessed directly
if (!defined('ABSPATH'))
  exit;

// Ensure WordPress functions are available
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/image.php');
require_once(ABSPATH . 'wp-admin/includes/media.php');

class VSAI_Handlers
{
  private $api_client;
  private $token_handler;

  public function __construct($api_client, $token_handler)
  {
    $this->api_client = $api_client;
    $this->token_handler = $token_handler;
  }

  public function hello_world_handler($request)
  {
    return array(
      'message' => 'Hello World'
    );
  }

  public function upload_image_handler($request)
  {
    error_log('VSAI: Starting image upload handler');

    $token = $request->get_param('token');

    if (!$token) {
      return new WP_Error('missing_token', 'Access token is missing', array('status' => 401));
    }

    if (!$this->token_handler->token_exists($token)) {
      return new WP_Error('invalid_token', 'Invalid access token', array('status' => 401));
    }

    if ($this->token_handler->is_limit_breached($token)) {
      return new WP_Error('limit_breached', 'Render limit reached', array('status' => 403));
    }

    $files = $request->get_file_params();

    error_log('VSAI: Files received: ' . print_r($files, true));

    if (!isset($files['image'])) {
      error_log('VSAI: No image file provided in the request');
      return new WP_Error('missing_image', 'No image file provided', array('status' => 400));
    }

    $upload_dir = wp_upload_dir();
    error_log('VSAI: Upload directory: ' . print_r($upload_dir, true));

    if (!wp_is_writable($upload_dir['path'])) {
      error_log('VSAI: Upload directory is not writable: ' . $upload_dir['path']);
      return new WP_Error('upload_error', 'Upload directory is not writable', array('status' => 500));
    }

    error_log('VSAI: Attempting to upload file');
    $upload = wp_handle_upload($files['image'], array('test_form' => false));

    if (isset($upload['error'])) {
      error_log('VSAI Image Upload Error: ' . $upload['error']);
      return new WP_Error('upload_error', $upload['error'], array('status' => 500));
    }

    error_log('VSAI: File uploaded successfully. Result: ' . print_r($upload, true));

    wp_schedule_single_event(time() + 3600, 'vsai_delete_uploaded_image', array($upload['file']));

    return array(
      'success' => true,
      'url' => $upload['url']
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
    $params = $request->get_params();

    if (!isset($params['image_url']) || !isset($params['room_type']) || !isset($params['style']) || !isset($params['token'])) {
      return new WP_Error('missing_params', 'Missing required parameters', array('status' => 400));
    }

    // Check if the token is valid and limit is not breached
    if (!$this->token_handler->token_exists($params['token'])) {
      return new WP_Error('invalid_token', 'Invalid access token', array('status' => 401));
    }

    if ($this->token_handler->is_limit_breached($params['token'])) {
      return new WP_Error('limit_breached', 'Render limit reached', array('status' => 403));
    }

    // Prepare data for Virtual Staging API
    $data = array(
      'image_url' => $params['image_url'],
      'room_type' => $params['room_type'],
      'style' => $params['style'],
      'wait_for_completion' => false
    );

    // Make request to Virtual Staging API
    $response = $this->api_client->request('render/create', 'POST', $data);

    // If the render creation was successful, increment the count
    if (isset($response['render_id'])) {
      $this->token_handler->increment_render_count($params['token']);
    }

    return $response;
  }

  public function get_render_status_handler($request)
  {
    $render_id = $request->get_param('render_id');
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

  public function generate_token_handler($request)
  {
    $limit = $request->get_param('limit');
    $limit = $this->normalize_limit($limit);
    $token = $this->token_handler->add_token($limit);
    if ($token) {
      return array(
        'success' => true,
        'token' => $token,
        'limit' => $limit
      );
    } else {
      return new WP_Error('token_generation_failed', 'Failed to generate a unique token after multiple attempts', array('status' => 500));
    }
  }

  private function normalize_limit($limit)
  {
    $limit = intval($limit);
    if ($limit < 5) {
      return 5;
    } elseif ($limit > 10) {
      return 10;
    }
    return $limit;
  }

  public function token_status_handler($request)
  {
    $token = $request->get_param('at');

    if (!$token) {
      return new WP_Error('missing_token', 'Access token is missing', array('status' => 400));
    }

    $status = $this->token_handler->get_token_status($token);

    if ($status === null) {
      return new WP_Error('invalid_token', 'Invalid access token', array('status' => 404));
    }

    return $status;
  }

  // Add more handler methods here as needed
}
