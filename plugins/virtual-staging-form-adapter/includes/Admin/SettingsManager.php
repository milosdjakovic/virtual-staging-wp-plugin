<?php

namespace VirtualStagingAdapter\Admin;

use VirtualStagingAdapter\Config\ConfigInterface;

class SettingsManager
{
    private $config;

    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    public function registerSettings()
    {
        register_setting('vsa_settings', 'vsa_forms', [
            'type' => 'array',
            'sanitize_callback' => [$this, 'sanitizeForms'],
        ]);
    }

    public function sanitizeForms($input)
    {
        $sanitized_input = [];
        if (is_array($input)) {
            foreach ($input as $form) {
                $sanitized_form = [
                    'form_id' => sanitize_text_field($form['form_id']),
                    'renders_field_id' => sanitize_text_field($form['renders_field_id']),
                    'renders_regex' => sanitize_text_field($form['renders_regex']),
                    'redirect_path' => sanitize_text_field($form['redirect_path']),
                ];
                $sanitized_input[] = $sanitized_form;
            }
        }
        return $sanitized_input;
    }

    public function getForms()
    {
        return $this->config->get('vsa_forms', []);
    }

    public function saveForms($forms)
    {
        $this->config->set('vsa_forms', $forms);
    }
}
