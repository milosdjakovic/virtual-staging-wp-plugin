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
        register_setting('vsa_settings', 'vsa_form_id');
        register_setting('vsa_settings', 'vsa_at_field_id');
        register_setting('vsa_settings', 'vsa_redirect_path');

        add_settings_section('vsa_main_section', '', null, 'vsa_settings');

        add_settings_field('vsa_form_id', 'Form ID', [$this, 'renderFormIdField'], 'vsa_settings', 'vsa_main_section');
        add_settings_field('vsa_at_field_id', 'AT Field ID', [$this, 'renderAtFieldIdField'], 'vsa_settings', 'vsa_main_section');
        add_settings_field('vsa_redirect_path', 'Redirect Path', [$this, 'renderRedirectPathField'], 'vsa_settings', 'vsa_main_section');
    }

    public function renderFormIdField()
    {
        $form_id = $this->config->get('vsa_form_id');
        echo "<input type='text' name='vsa_form_id' value='$form_id' />";
        echo "<p class='description'>Enter the WPForms Form ID that should trigger the redirection.</p>";
    }

    public function renderAtFieldIdField()
    {
        $at_field_id = $this->config->get('vsa_at_field_id');
        echo "<input type='text' name='vsa_at_field_id' value='$at_field_id' />";
        echo "<p class='description'>Enter the ID of the form field to use as the 'at' parameter value.</p>";
    }

    public function renderRedirectPathField()
    {
        $redirect_path = $this->config->get('vsa_redirect_path');
        echo "<input type='text' name='vsa_redirect_path' value='$redirect_path' />";
        echo "<p class='description'>Enter the path where users will be redirected after successfully submitting the form. Start with a forward slash, e.g., /virtual-staging-upload/</p>";
    }
}
