<?php

namespace VirtualStagingAdapter\Admin;

use VirtualStagingAdapter\Admin\Templates\SettingsTemplate;
use VirtualStagingAdapter\Admin\Templates\TokenGenerationTemplate;
use VirtualStagingAdapter\Admin\Templates\EnvironmentInfoTemplate;
use VirtualStagingAdapter\Config\ConfigInterface;
use VirtualStagingAdapter\Service\TokenService;
use VirtualStagingAdapter\Service\RedirectService;

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
    $this->templates['environmentInfo'] = new EnvironmentInfoTemplate();
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

      <?php $this->templates['environmentInfo']->render(); ?>

      <?php $this->templates['tokenGeneration']->renderTopNotice(); ?>

      <?php $this->templates['settings']->render(); ?>

      <?php $this->templates['tokenGeneration']->render(); ?>
    </div>
    <?php
  }
}
