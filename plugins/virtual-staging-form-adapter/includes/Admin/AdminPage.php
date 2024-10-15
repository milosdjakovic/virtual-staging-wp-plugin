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
  private $generationResult = '';

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
    if (isset($_POST['generate_token']) && check_admin_referer('vsa_generate_token', 'vsa_token_nonce')) {
      $this->handleTokenGeneration();
    }

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

      <h2>Token Generation</h2>
      <p>Use this section to generate a token and view the resulting redirect URL. After submitting, the results will appear
        in a notice at the top of the page.</p>
      <form method="post" action="">
        <?php wp_nonce_field('vsa_generate_token', 'vsa_token_nonce'); ?>
        <label for="render_limit">Render Limit (5-10):</label>
        <input type="number" id="render_limit" name="render_limit" min="5" max="10" value="5"
          style="width: 60px; margin-right: 10px;" />
        <input type="submit" name="generate_token" class="button button-primary" value="Generate Token and URL" />
      </form>

      <div id="token-generation-result" style="margin-top: 20px;">
        <?php echo $this->generationResult; ?>
      </div>
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

      $this->generationResult = '<div class="notice notice-success">';
      $this->generationResult .= '<p><strong>Token generated successfully!</strong></p>';
      $this->generationResult .= '<p><strong>Token:</strong> ' . esc_html($token) . '</p>';
      $this->generationResult .= '<p><strong>Redirect URL:</strong> <a href="' . esc_url($redirectUrl) . '" target="_blank">' . esc_html($redirectUrl) . '</a></p>';
      $this->generationResult .= '</div>';
    } else {
      $this->generationResult = '<div class="notice notice-error"><p>Failed to generate token. Please try again.</p></div>';
    }
  }
}
