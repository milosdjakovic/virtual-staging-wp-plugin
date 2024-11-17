<?php

namespace VirtualStagingAdapter\Admin\Templates;

use VirtualStagingAdapter\Plugin;

class EnvironmentInfoTemplate
{
  public function render()
  {
    $envManager = Plugin::getEnvironmentManager();
    $isDev = $envManager->isDev();
    $locale = $envManager->getLocale();

    if ($isDev) {
      $pluginRoot = dirname(dirname(dirname(dirname(__FILE__)))); // Go up three levels to the plugin root
      $envFile = $pluginRoot . '/.env';
      $fileExists = file_exists($envFile) ? 'Yes' : 'No';

      ?>
      <div class="notice notice-warning">
        <h2>Development Environment Active</h2>
        <p>Plugin is running in development environment.</p>
        <p><strong>DEV_MODE:</strong> true</p>
        <p><strong>LOCALE:</strong> <?php echo esc_html($envManager->get('LOCALE', 'en.json')); ?></p>
        <p><strong>.env File Path:</strong> <?php echo esc_html($envFile); ?></p>
      </div>
      <?php
    }
  }
}
