<?php
/*
Plugin Name: API Test Plugin
Description: A simple plugin to test API GET requests
Version: 1.0
Author: Your Name
*/

if (!defined('ABSPATH')) {
  exit;
}

class API_Test_Plugin
{
  public function __construct()
  {
    add_action('admin_menu', array($this, 'add_admin_menu'));
  }

  public function add_admin_menu()
  {
    add_menu_page(
      'API Test',
      'API Test',
      'manage_options',
      'api-test-plugin',
      array($this, 'display_test_page'),
      'dashicons-admin-generic',
      100
    );
  }

  public function display_test_page()
  {
    ?>
    <div class="wrap">
      <h1>API Test</h1>
      <form method="post" action="">
        <?php wp_nonce_field('api_test_nonce', 'api_test_nonce_field'); ?>
        <input type="submit" name="run_test" class="button button-primary" value="Run API Test">
      </form>
      <?php
      if (isset($_POST['run_test']) && check_admin_referer('api_test_nonce', 'api_test_nonce_field')) {
        $result = $this->run_api_test();
        echo '<h2>Test Results:</h2>';
        echo '<pre>' . esc_html($result) . '</pre>';

        echo '<h2>Error Logs:</h2>';
        echo '<pre>' . esc_html($this->get_error_logs()) . '</pre>';
      }
      ?>
    </div>
    <?php
  }


  private function run_api_test()
  {
    $url = rest_url('vsai/v1/options');

    $result = "Requested URL: " . $url . "\n\n";

    // Use an internal request instead of wp_remote_get
    $request = new WP_REST_Request('GET', '/vsai/v1/options');
    $response = rest_do_request($request);

    if (is_wp_error($response)) {
      $result .= 'Error: ' . $response->get_error_message();
    } else {
      $data = $response->get_data();

      $result .= "HTTP Response Code: " . $response->get_status() . "\n\n";
      $result .= "Response Headers:\n" . print_r($response->get_headers(), true) . "\n\n";
      $result .= "Response Body:\n" . print_r($data, true);

      if (isset($data['token'])) {
        $result .= "\n\nSuccess! Token: " . $data['token'];
      } else {
        $result .= "\n\nToken not found in the response.";
      }
    }

    return $result;
  }

  private function get_error_logs()
  {
    $log_file = WP_CONTENT_DIR . '/debug.log';
    if (file_exists($log_file)) {
      return "Recent error logs:\n" . shell_exec("tail -n 50 $log_file");
    }
    return "No error logs found.";
  }

}



// Initialize the plugin
$api_test_plugin = new API_Test_Plugin();
