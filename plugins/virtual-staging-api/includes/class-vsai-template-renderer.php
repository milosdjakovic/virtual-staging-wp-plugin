<?php
// Exit if accessed directly
if (!defined('ABSPATH'))
  exit;

class VSAI_Template_Renderer
{
  private $templates = [];
  private $plugin_url;

  public function __construct($plugin_url)
  {
    $this->plugin_url = $plugin_url;
    add_shortcode('vsai_template', array($this, 'render_template_shortcode'));
    add_action('wp_enqueue_scripts', array($this, 'enqueue_template_assets'));
  }

  public function register_template($name, $file_path)
  {
    if (file_exists($file_path)) {
      $this->templates[$name] = $file_path;
    } else {
      error_log("VSAI: Template file not found: $file_path");
    }
  }

  public function render_template($name, $data = [])
  {
    if (!isset($this->templates[$name])) {
      return "Template '$name' not found.";
    }

    ob_start();
    extract($data);
    include $this->templates[$name];
    return ob_get_clean();
  }

  public function render_template_shortcode($atts)
  {
    $atts = shortcode_atts(array(
      'name' => '',
      'data' => '{}',
    ), $atts);

    if (empty($atts['name'])) {
      return "Template name not specified.";
    }

    $data = json_decode($atts['data'], true);
    if (json_last_error() !== JSON_ERROR_NONE) {
      $data = [];
    }

    return $this->render_template($atts['name'], $data);
  }

  public function enqueue_template_assets()
  {
    // Enqueue main CSS
    wp_enqueue_style('vsai-main-style', $this->plugin_url . 'templates/main/snipped.css');
    wp_enqueue_style('vsai-upload-style', $this->plugin_url . 'templates/upload/snipped.css');

    // Enqueue any JavaScript files if needed
    // wp_enqueue_script('vsai-main-script', $this->plugin_url . 'templates/main/script.js', array('jquery'), null, true);
    // wp_enqueue_script('vsai-upload-script', $this->plugin_url . 'templates/upload/script.js', array('jquery'), null, true);
  }
}
