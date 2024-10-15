<?php

namespace VirtualStagingAdapter\Admin\Templates;

use VirtualStagingAdapter\Service\TokenService;
use VirtualStagingAdapter\Service\RedirectService;
use VirtualStagingAdapter\Config\ConfigInterface;

class TokenGenerationTemplate
{
  private $tokenService;
  private $redirectService;
  private $config;
  private $generationResult = null;

  public function __construct(TokenService $tokenService, RedirectService $redirectService, ConfigInterface $config)
  {
    $this->tokenService = $tokenService;
    $this->redirectService = $redirectService;
    $this->config = $config;
  }

  public function handleRequest()
  {
    if (isset($_POST['generate_token']) && check_admin_referer('vsa_generate_token', 'vsa_token_nonce')) {
      $this->handleTokenGeneration();
    }
  }

  public function render()
  {
    ?>
    <h2>Token Generation</h2>
    <p>Use this section to generate a token and view the resulting redirect URLs for all configured forms.</p>
    <form method="post" action="">
      <?php wp_nonce_field('vsa_generate_token', 'vsa_token_nonce'); ?>
      <label for="render_limit">Render Limit (5-10):</label>
      <input type="number" id="render_limit" name="render_limit" min="5" max="10" value="5"
        style="width: 60px; margin-right: 10px;" />
      <input type="submit" name="generate_token" class="button button-primary" value="Generate Token and URLs" />
    </form>

    <?php $this->renderCustomStyledResult(); ?>

    <style>
      .vsa-result {
        background-color: #f0f0f1;
        border-left: 4px solid #72aee6;
        box-shadow: 0 1px 1px rgba(0, 0, 0, .04);
        margin: 20px 0;
        padding: 12px;
      }

      .vsa-result p {
        margin: 0.5em 0;
        padding: 2px;
      }

      .vsa-result .vsa-token {
        background-color: #e7e7e7;
        padding: 5px;
        font-family: monospace;
        word-break: break-all;
      }

      .vsa-result table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
      }

      .vsa-result th,
      .vsa-result td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
      }

      .vsa-result th {
        background-color: #f2f2f2;
      }

      .vsa-result .vsa-url {
        word-break: break-all;
      }
    </style>
    <?php
  }

  public function renderTopNotice()
  {
    if ($this->generationResult === null)
      return;

    $class = $this->generationResult['success'] ? 'notice-success' : 'notice-error';
    $content = $this->getResultContent();

    echo "<div class='notice {$class}'>{$content}</div>";
  }

  private function handleTokenGeneration()
  {
    $limit = isset($_POST['render_limit']) ? intval($_POST['render_limit']) : 5;
    $limit = max(5, min(10, $limit)); // Ensure limit is between 5 and 10

    $token = $this->tokenService->generateToken($limit);

    if ($token) {
      $forms = $this->config->get('vsa_forms', []);
      $redirectUrls = [];

      foreach ($forms as $form) {
        $redirectPath = $form['redirect_path'] ?? '';
        if (!empty($redirectPath)) {
          $redirectUrls[] = [
            'form_id' => $form['form_id'],
            'url' => $this->redirectService->getRedirectUrl($redirectPath, $token)
          ];
        }
      }

      $this->generationResult = [
        'success' => true,
        'token' => $token,
        'redirectUrls' => $redirectUrls
      ];
    } else {
      $this->generationResult = [
        'success' => false
      ];
    }
  }

  private function renderCustomStyledResult()
  {
    if ($this->generationResult === null)
      return;

    $content = $this->getResultContent();

    echo "<div class='vsa-result'>{$content}</div>";
  }

  private function getResultContent()
  {
    if (!$this->generationResult['success']) {
      return '<p>Failed to generate token. Please try again.</p>';
    }

    $token = esc_html($this->generationResult['token']);
    $content = "<p><strong>Token generated successfully!</strong></p>";
    $content .= "<p><strong>Token:</strong> <span class='vsa-token'>{$token}</span></p>";

    if (!empty($this->generationResult['redirectUrls'])) {
      $content .= "<p><strong>Redirect URLs for configured forms:</strong></p>";
      $content .= "<table>";
      $content .= "<tr><th>Form ID</th><th>Redirect URL</th></tr>";
      foreach ($this->generationResult['redirectUrls'] as $redirectData) {
        $formId = esc_html($redirectData['form_id']);
        $url = esc_url($redirectData['url']);
        $content .= "<tr>";
        $content .= "<td>{$formId}</td>";
        $content .= "<td><a href='{$url}' target='_blank' class='vsa-url'>{$url}</a></td>";
        $content .= "</tr>";
      }
      $content .= "</table>";
    } else {
      $content .= "<p>No forms configured for redirection.</p>";
    }

    return $content;
  }
}
