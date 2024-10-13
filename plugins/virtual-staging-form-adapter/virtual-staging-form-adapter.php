<?php
/*
Plugin Name: Virtual Staging API Form Adapter
Description: Redirects to a specified path after WPForms submission
Version: 1.2
Author: Your Name
*/

// Add admin menu
function vsa_add_admin_menu()
{
  add_menu_page(
    'Virtual Staging API Form Adapter',
    'Virtual Staging API Form Adapter',
    'manage_options',
    'virtual-staging-adapter',
    'vsa_admin_page',
    'dashicons-admin-generic',
    60
  );
}
add_action('admin_menu', 'vsa_add_admin_menu');

// Admin page content
function vsa_admin_page()
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
  </div>
  <?php
}

// Register settings
function vsa_register_settings()
{
  register_setting('vsa_settings', 'vsa_form_id');
  register_setting('vsa_settings', 'vsa_redirect_path');

  add_settings_section('vsa_main_section', '', null, 'vsa_settings');

  add_settings_field('vsa_form_id', 'Form ID', 'vsa_form_id_callback', 'vsa_settings', 'vsa_main_section');
  add_settings_field('vsa_redirect_path', 'Redirect Path', 'vsa_redirect_path_callback', 'vsa_settings', 'vsa_main_section');
}
add_action('admin_init', 'vsa_register_settings');

// Form ID field callback
function vsa_form_id_callback()
{
  $form_id = get_option('vsa_form_id');
  echo "<input type='text' name='vsa_form_id' value='$form_id' />";
  echo "<p class='description'>Enter the WPForms Form ID that should trigger the redirection.</p>";
}

// Redirect path field callback
function vsa_redirect_path_callback()
{
  $redirect_path = get_option('vsa_redirect_path');
  echo "<input type='text' name='vsa_redirect_path' value='$redirect_path' />";
  echo "<p class='description'>Enter the path where users will be redirected after successfully submitting the form. Start with a forward slash, e.g., /virtual-staging-upload/</p>";
  echo "<p class='description'>This is the destination path on your website where users will be sent after the form is successfully submitted.</p>";
}

// Handle form submission and redirection
function vsa_handle_form_submission($fields, $entry, $form_data, $entry_id)
{
  $vsa_form_id = get_option('vsa_form_id');
  $vsa_redirect_path = get_option('vsa_redirect_path');

  if ($form_data['id'] == $vsa_form_id && !empty($vsa_redirect_path)) {
    wp_redirect(home_url($vsa_redirect_path));
    exit;
  }
}
add_action('wpforms_process_complete', 'vsa_handle_form_submission', 10, 4);
