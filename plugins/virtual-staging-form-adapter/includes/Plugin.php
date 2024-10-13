<?php

namespace VirtualStagingAdapter;

use VirtualStagingAdapter\Admin\AdminPage;
use VirtualStagingAdapter\Admin\SettingsManager;
use VirtualStagingAdapter\Config\WordPressConfig;
use VirtualStagingAdapter\Form\FormHandler;
use VirtualStagingAdapter\Service\RedirectService;

class Plugin
{
  private $adminPage;
  private $settingsManager;
  private $formHandler;

  public function __construct()
  {
    $config = new WordPressConfig();
    $redirectService = new RedirectService($config);

    $this->settingsManager = new SettingsManager($config);
    $this->adminPage = new AdminPage($this->settingsManager);
    $this->formHandler = new FormHandler($config, $redirectService);
  }

  public function run()
  {
    add_action('admin_menu', [$this->adminPage, 'addMenuPage']);
    add_action('admin_init', [$this->settingsManager, 'registerSettings']);
    add_action('wpforms_process_complete', [$this->formHandler, 'handleSubmission'], 10, 4);
  }
}
