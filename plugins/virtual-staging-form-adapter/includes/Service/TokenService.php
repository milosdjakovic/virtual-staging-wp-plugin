<?php

namespace VirtualStagingAdapter\Service;

class TokenService
{
  private $api_base_url;

  public function __construct()
  {
    $this->api_base_url = site_url(VSA_API_PATH);
  }

  public function getTokenUrl($limit)
  {
    return $this->api_base_url . "/generate-token?limit=" . urlencode($limit);
  }
}
