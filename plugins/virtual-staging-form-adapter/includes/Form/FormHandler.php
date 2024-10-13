<?php

namespace VirtualStagingAdapter\Form;

use VirtualStagingAdapter\Config\ConfigInterface;
use VirtualStagingAdapter\Service\RedirectService;

class FormHandler
{
  private $config;
  private $redirectService;

  public function __construct(ConfigInterface $config, RedirectService $redirectService)
  {
    $this->config = $config;
    $this->redirectService = $redirectService;
  }

  public function handleSubmission($fields, $entry, $form_data, $entry_id)
  {
    $vsa_form_id = $this->config->get('vsa_form_id');
    $vsa_redirect_path = $this->config->get('vsa_redirect_path');

    if ($form_data['id'] == $vsa_form_id && !empty($vsa_redirect_path)) {
      $this->redirectService->redirect($vsa_redirect_path);
    }
  }
}
