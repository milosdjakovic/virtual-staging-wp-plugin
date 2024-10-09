<?php
// Exit if accessed directly
if (!defined('ABSPATH'))
  exit;

class VSAI_Template_Renderer
{
  private $templates = [];

  public function __construct()
  {
    add_shortcode('vsai_template', array($this, 'render_template_shortcode'));
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

  public function enqueue_template_styles()
  {
    wp_enqueue_style('vsai-template-styles', plugin_dir_url(__FILE__) . '../assets/css/vsai-templates.css');
  }
}
