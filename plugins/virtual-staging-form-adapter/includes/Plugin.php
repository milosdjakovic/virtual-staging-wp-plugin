<?php

namespace VirtualStagingAdapter;

use VirtualStagingAdapter\Admin\AdminPage;
use VirtualStagingAdapter\Admin\SettingsManager;
use VirtualStagingAdapter\Config\WordPressConfig;
use VirtualStagingAdapter\Form\FormHandler;
use VirtualStagingAdapter\Service\RedirectService;
use VirtualStagingAdapter\Service\TokenService;

class Plugin
{
  private $adminPage;
  private $settingsManager;
  private $formHandler;
  private static $envManager;

  public function __construct()
  {
    self::$envManager = EnvironmentManager::getInstance();

    $config = new WordPressConfig();
    $tokenService = new TokenService();
    $redirectService = new RedirectService($config, $tokenService);

    $this->settingsManager = new SettingsManager($config);

    $this->adminPage = new AdminPage($config, $this->settingsManager, $tokenService, $redirectService);
    $this->formHandler = new FormHandler($config, $redirectService);
  }

  public function run()
  {
    add_action('admin_menu', [$this->adminPage, 'addMenuPage']);
    add_action('admin_init', [$this->settingsManager, 'registerSettings']);
    add_action('wpforms_process_complete', [$this->formHandler, 'handleSubmission'], 10, 4);
  }

  public static function getEnvironmentManager()
  {
    return self::$envManager;
  }
}
