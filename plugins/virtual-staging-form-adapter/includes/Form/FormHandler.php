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
    $vsa_renders_field_id = $this->config->get('vsa_renders_field_id');
    $vsa_renders_regex = $this->config->get('vsa_renders_regex');

    if ($form_data['id'] == $vsa_form_id && !empty($vsa_redirect_path)) {
      $renders_value = '';
      foreach ($fields as $field) {
        if ($field['id'] == $vsa_renders_field_id) {
          $value_to_check = $this->getValueToCheck($field['value']);
          if (preg_match($vsa_renders_regex, $value_to_check, $matches)) {
            $renders_value = $matches[1];
          }
          break;
        }
      }

      if (!empty($renders_value)) {
        $this->redirectService->redirect($vsa_redirect_path, $renders_value);
      }
    }
  }

  private function getValueToCheck($fieldValue)
  {
    if (is_string($fieldValue)) {
      return $fieldValue;
    } elseif (is_array($fieldValue)) {
      if (isset($fieldValue['value'])) {
        // It's an associative array
        return $fieldValue['value'];
      } elseif (isset($fieldValue[0]) && is_array($fieldValue[0]) && isset($fieldValue[0]['value'])) {
        // It's an array of associative arrays
        return $fieldValue[0]['value'];
      }
    }
    // If we can't determine the value, return an empty string
    return '';
  }
}
