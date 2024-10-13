<?php

namespace VirtualStagingAdapter\Admin;

class AdminPage
{
  private $settingsManager;

  public function __construct(SettingsManager $settingsManager)
  {
    $this->settingsManager = $settingsManager;
  }

  public function addMenuPage()
  {
    add_menu_page(
      'Virtual Staging API Form Adapter',
      'Virtual Staging API Form Adapter',
      'manage_options',
      'virtual-staging-adapter',
      [$this, 'renderPage'],
      'dashicons-admin-generic',
      60
    );
  }

  public function renderPage()
  {
    ?>
    <div class="wrap">
      <h1>Virtual Staging API Form Adapter</h1>
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
    </div>
    <?php
  }
}
