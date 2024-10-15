<?php

namespace VirtualStagingAdapter\Admin;

use VirtualStagingAdapter\Service\TokenService;
use VirtualStagingAdapter\Service\RedirectService;
use VirtualStagingAdapter\Config\ConfigInterface;

class AdminPage
{
  private $settingsManager;
  private $tokenService;
  private $redirectService;
  private $config;

  public function __construct(SettingsManager $settingsManager, TokenService $tokenService, RedirectService $redirectService, ConfigInterface $config)
  {
    $this->settingsManager = $settingsManager;
    $this->tokenService = $tokenService;
    $this->redirectService = $redirectService;
    $this->config = $config;
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

      <h2>Token Generation Test</h2>
      <p>Use this section to test token generation and view the resulting redirect URL.</p>
      <form method="post" action="">
        <?php wp_nonce_field('vsa_generate_token', 'vsa_token_nonce'); ?>
        <input type="number" name="render_limit" min="5" max="10" value="5" />
        <input type="submit" name="generate_token" class="button button-primary" value="Generate Token and URL" />
      </form>

      <?php
      if (isset($_POST['generate_token']) && check_admin_referer('vsa_generate_token', 'vsa_token_nonce')) {
        $this->handleTokenGeneration();
      }
      ?>
    </div>
    <?php
  }

  private function handleTokenGeneration()
  {
    $limit = isset($_POST['render_limit']) ? intval($_POST['render_limit']) : 5;
    $limit = max(5, min(10, $limit)); // Ensure limit is between 5 and 10

    $token = $this->tokenService->generateToken($limit);

    if ($token) {
      $redirectPath = $this->config->get('vsa_redirect_path');
      $redirectUrl = $this->redirectService->getRedirectUrl($redirectPath, $token);

      echo '<div class="notice notice-success"><p>Token generated successfully!</p>';
      echo '<p>Token: ' . esc_html($token) . '</p>';
      echo '<p>Redirect URL: <a href="' . esc_url($redirectUrl) . '" target="_blank">' . esc_html($redirectUrl) . '</a></p></div>';
    } else {
      echo '<div class="notice notice-error"><p>Failed to generate token. Please try again.</p></div>';
    }
  }
}
