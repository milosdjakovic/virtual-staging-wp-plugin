<?php

namespace VirtualStagingAdapter\Service;

class TokenService
{
  private $api_base_url;

  public function __construct()
  {
    $this->api_base_url = site_url(VSA_API_PATH);
  }

  public function generateToken($limit)
  {
    $url = $this->api_base_url . "/generate-token?limit=" . urlencode($limit);

    $request = new \WP_REST_Request('GET', '/vsai/v1/generate-token');
    $request->set_query_params(['limit' => $limit]);

    $response = rest_do_request($request);

    if (is_wp_error($response)) {
      error_log('Token generation failed: ' . $response->get_error_message());
      return null;
    }

    $data = $response->get_data();

    if (isset($data['token'])) {
      return $data['token'];
    }

    error_log('Token not found in the response: ' . print_r($data, true));
    return null;
  }
}
