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
    $forms = $this->config->get('vsa_forms', []);

    foreach ($forms as $form) {
      if ($form_data['id'] == $form['form_id'] && !empty($form['redirect_path'])) {
        $renders_value = $this->extractRendersValue($fields, $form['renders_field_id'], $form['renders_regex'] ?? '');

        if (!empty($renders_value)) {
          $this->redirectService->redirect($form['redirect_path'], $renders_value);
          return; // Exit after successful redirect
        }
      }
    }
  }

  private function extractRendersValue($fields, $renders_field_id, $renders_regex)
  {
    foreach ($fields as $field) {
      if ($field['id'] == $renders_field_id) {
        $value_to_check = $this->getValueToCheck($field['value']);

        if (empty($renders_regex)) {
          // If no regex is specified, return the value as is
          return $value_to_check;
        } elseif (preg_match($renders_regex, $value_to_check, $matches)) {
          return isset($matches[1]) ? $matches[1] : $matches[0];
        }
      }
    }
    return '';
  }

  private function getValueToCheck($fieldValue)
  {
    if (is_string($fieldValue) || is_numeric($fieldValue)) {
      return $fieldValue;
    } elseif (is_array($fieldValue)) {
      if (isset($fieldValue['value'])) {
        return $fieldValue['value'];
      } elseif (isset($fieldValue[0]) && is_array($fieldValue[0]) && isset($fieldValue[0]['value'])) {
        return $fieldValue[0]['value'];
      }
    }
    return '';
  }
}
