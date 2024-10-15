<?php

namespace VirtualStagingAdapter\Admin\Templates;

use VirtualStagingAdapter\Admin\SettingsManager;

class SettingsTemplate
{
  private $settingsManager;

  public function __construct(SettingsManager $settingsManager)
  {
    $this->settingsManager = $settingsManager;
  }

  public function render()
  {
    ?>
    <h2>WPForm Configuration</h2>
    <p>This section configures the integration with the WPForms plugin. It determines which form triggers the redirection
      and where users are sent after successful submission.</p>
    <form method="post" action="options.php">
      <?php
      settings_fields('vsa_settings');
      do_settings_sections('vsa_settings');
      submit_button();
      ?>
    </form>
    <?php
  }
}
