<?php
// Exit if accessed directly
if (!defined('ABSPATH'))
  exit;

class VSAI_Template_Renderer
{
  private $templates = [];
  private $plugin_url;
  private $used_templates = [];
  private $api_client;
  private $options_formatter;
  private $translations;

  public function __construct($plugin_url, $api_client, $translations)
  {
    $this->plugin_url = $plugin_url;
    $this->api_client = $api_client;
    $this->options_formatter = new VSAI_Options_Formatter($translations);
    $this->translations = $translations;
    add_action('wp_enqueue_scripts', array($this, 'register_template_assets'));
    add_action('wp_footer', array($this, 'enqueue_template_assets'), 5);
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

    $this->used_templates[$name] = true;

    // Add translations to data
    $data['translations'] = $this->translations;
    $data['options'] = $this->fetch_options();

    ob_start();
    
    // Output translations script before template
    echo $this->inject_translations($this->translations);

    // Extract data to make variables available in template
    extract($data);
    
    include $this->templates[$name];
    return ob_get_clean();
  }

  private function fetch_options()
  {
    $options = $this->api_client->request('options');
    if (is_wp_error($options)) {
      error_log('Failed to fetch options: ' . $options->get_error_message());
      return array('styles' => array(), 'roomTypes' => array());
    }
    return $options;
  }

  public function generate_select_options($options)
  {
    $type = strpos(json_encode($options), 'modern') !== false ? 'styles' : 'roomTypes';
    $formatted = $this->options_formatter->format($options, $type);
    return $this->options_formatter->to_html($formatted);
  }

  public function register_template_assets()
  {
    wp_register_style('vsai-main-style', $this->plugin_url . 'templates/main/snipped.css');
    wp_register_style('vsai-upload-style', $this->plugin_url . 'templates/upload/snipped.css');

    wp_register_script('vsai-main-script', $this->plugin_url . 'templates/main/script.js', array('jquery'), null, true);
    wp_register_script('vsai-upload-script', $this->plugin_url . 'templates/upload/script.js', array('jquery'), null, true);
    wp_register_script('vsai-test-script', $this->plugin_url . 'templates/test/script.js', array('jquery'), null, true);
  }

  public function enqueue_template_assets()
  {
    if (isset($this->used_templates['main'])) {
      wp_enqueue_style('vsai-main-style');
      wp_enqueue_script('vsai-main-script', $this->plugin_url . 'templates/main/script.js', array('jquery'), null, true);
    }
    if (isset($this->used_templates['upload'])) {
      wp_enqueue_style('vsai-upload-style');
      wp_enqueue_script('vsai-upload-script', $this->plugin_url . 'templates/upload/script.js', array('jquery'), null, true);
    }
    if (isset($this->used_templates['test'])) {
      wp_enqueue_script('vsai-test-script', $this->plugin_url . 'templates/test/script.js', array('jquery'), null, true);
    }
  }

  private function inject_translations($translations)
  {
    $json = wp_json_encode($translations, JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS);
    return sprintf(
      '<script type="text/javascript">window.vsaiTranslations = %s;</script>',
      $json
    );
  }

}
