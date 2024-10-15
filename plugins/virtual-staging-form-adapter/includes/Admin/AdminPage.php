<?php

namespace VirtualStagingAdapter\Admin;

use VirtualStagingAdapter\Admin\Components\SettingsComponent;
use VirtualStagingAdapter\Admin\Components\TokenGenerationComponent;

class AdminPage
{
  private $settingsComponent;
  private $tokenGenerationComponent;

  public function __construct(SettingsComponent $settingsComponent, TokenGenerationComponent $tokenGenerationComponent)
  {
    $this->settingsComponent = $settingsComponent;
    $this->tokenGenerationComponent = $tokenGenerationComponent;
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
    $this->tokenGenerationComponent->handleRequest();

    ?>
    <div class="wrap">
      <h1>Virtual Staging API Form Adapter</h1>

      <?php $this->tokenGenerationComponent->renderTopNotice(); ?>

      <?php $this->settingsComponent->render(); ?>

      <?php $this->tokenGenerationComponent->render(); ?>
    </div>
    <?php
  }
}
