<?php

namespace VirtualStagingAdapter\Admin\Templates;

use VirtualStagingAdapter\Plugin;

class EnvironmentInfoTemplate
{
  public function render()
  {
    $envManager = Plugin::getEnvironmentManager();
    $isDev = $envManager->isDev();
    $envFile = $envManager->getEnvFilePath();
    $fileExists = file_exists($envFile) ? 'Yes' : 'No';

    ?>
    <div class="notice <?php echo $isDev ? 'notice-warning' : 'notice-info'; ?>">
      <h2>Environment Information</h2>
      <p>Current environment: <strong><?php echo $isDev ? 'Development' : 'Production'; ?></strong></p>
      <p><strong>DEV_MODE:</strong> <?php echo $isDev ? 'true' : 'false'; ?></p>
      <p><strong>LOCALE:</strong> <?php echo esc_html($envManager->get('LOCALE', 'en.json')); ?></p>
      <p><strong>.env File Path:</strong> <?php echo esc_html($envFile); ?></p>
      <p><strong>.env File Exists:</strong> <?php echo $fileExists; ?></p>
    </div>
    <?php
  }
}
