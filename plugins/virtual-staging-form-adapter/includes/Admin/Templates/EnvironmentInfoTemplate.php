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
    $fileExists = file_exists($envFile);

    ?>
    <div class="notice <?php echo $isDev ? 'notice-warning' : 'notice-info'; ?>">
      <h2>Environment Information</h2>
      <p>Current environment: <strong><?php echo $isDev ? 'Development' : 'Production'; ?></strong></p>
      <p><strong>DEV_MODE:</strong> <?php echo $isDev ? 'true' : 'false'; ?></p>
      <p><strong>LOCALE:</strong> <?php echo esc_html($envManager->get('LOCALE', 'en.json')); ?></p>
      <?php if ($fileExists): ?>
        <p><strong>.env File Path:</strong> <?php echo esc_html($envFile); ?></p>
      <?php else: ?>
        <p><strong>.env File Exists:</strong> No</p>
        <p><strong>Expected .env Location:</strong> <?php echo esc_html($envFile); ?></p>
      <?php endif; ?>
    </div>
    <?php
  }
}
