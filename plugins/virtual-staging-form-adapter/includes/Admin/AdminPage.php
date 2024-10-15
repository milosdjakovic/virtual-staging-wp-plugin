<?php

namespace VirtualStagingAdapter\Admin;

use VirtualStagingAdapter\Admin\Templates\SettingsTemplate;
use VirtualStagingAdapter\Admin\Templates\TokenGenerationTemplate;
use VirtualStagingAdapter\Config\ConfigInterface;
use VirtualStagingAdapter\Service\TokenService;
use VirtualStagingAdapter\Service\RedirectService;
use VirtualStagingAdapter\Plugin;

class AdminPage
{
  private $config;
  private $settingsManager;
  private $tokenService;
  private $redirectService;
  private $templates = [];

  public function __construct(
    ConfigInterface $config,
    SettingsManager $settingsManager,
    TokenService $tokenService,
    RedirectService $redirectService
  ) {
    $this->config = $config;
    $this->settingsManager = $settingsManager;
    $this->tokenService = $tokenService;
    $this->redirectService = $redirectService;

    $this->registerTemplates();
  }

  private function registerTemplates()
  {
    $this->templates['settings'] = new SettingsTemplate($this->settingsManager);
    $this->templates['tokenGeneration'] = new TokenGenerationTemplate(
      $this->tokenService,
      $this->redirectService,
      $this->config
    );
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
    $this->templates['tokenGeneration']->handleRequest();

    ?>
    <div class="wrap">
      <h1>Virtual Staging API Form Adapter</h1>

      <?php $this->renderEnvironmentInfo(); ?>

      <?php $this->templates['tokenGeneration']->renderTopNotice(); ?>

      <?php $this->templates['settings']->render(); ?>

      <?php $this->templates['tokenGeneration']->render(); ?>
    </div>
    <?php
  }

  private function renderEnvironmentInfo()
  {
    $envManager = Plugin::getEnvironmentManager();
    $isDev = $envManager->isDev();
    $devModeValue = $isDev ? 'true' : 'false';
    $envClass = $isDev ? 'notice-warning' : 'notice-info';

    ?>
    <div class="notice <?php echo $envClass; ?> is-dismissible">
      <h2>Environment Information</h2>
      <p>This section displays the current environment settings for the plugin.</p>
      <p><strong>DEV_MODE:</strong> <?php echo $devModeValue; ?></p>
    </div>
    <?php
  }
}
