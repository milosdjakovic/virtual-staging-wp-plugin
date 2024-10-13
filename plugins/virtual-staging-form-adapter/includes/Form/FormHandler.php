<?php

namespace VirtualStagingAdapter\Form;

use VirtualStagingAdapter\Config\ConfigInterface;
use VirtualStagingAdapter\Service\RedirectService;
use VirtualStagingAdapter\Service\TokenService;

class FormHandler
{
  private $config;
  private $redirectService;
  private $tokenService;

  public function __construct(ConfigInterface $config, RedirectService $redirectService, TokenService $tokenService)
  {
    $this->config = $config;
    $this->redirectService = $redirectService;
    $this->tokenService = $tokenService;
  }

  public function handleSubmission($fields, $entry, $form_data, $entry_id)
  {
    $vsa_form_id = $this->config->get('vsa_form_id');
    $vsa_redirect_path = $this->config->get('vsa_redirect_path');
    $vsa_renders_field_id = $this->config->get('vsa_renders_field_id');
    $vsa_renders_regex = $this->config->get('vsa_renders_regex');

    if ($form_data['id'] == $vsa_form_id && !empty($vsa_redirect_path)) {
      $renders_value = '';
      foreach ($fields as $field) {
        if ($field['id'] == $vsa_renders_field_id) {
          if (preg_match($vsa_renders_regex, $field['value'], $matches)) {
            $renders_value = $matches[1];
          }
          break;
        }
      }

      if (!empty($renders_value)) {
        $token_url = $this->tokenService->getTokenUrl($renders_value);
        $this->redirectService->redirect($vsa_redirect_path, $token_url);
      }
    }
  }
}
