<?php
/*
Plugin Name: Virtual Staging API Form Adapter
Description: Redirects to /virtual-staging-upload/ after WPForms submission
Version: 1.0
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
    <h1>WPForm Adapter</h1>
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
  add_settings_section('vsa_main_section', 'Settings', null, 'vsa_settings');
  add_settings_field('vsa_form_id', 'WPForms Form ID', 'vsa_form_id_callback', 'vsa_settings', 'vsa_main_section');
}
add_action('admin_init', 'vsa_register_settings');

// Form ID field callback
function vsa_form_id_callback()
{
  $form_id = get_option('vsa_form_id');
  echo "<input type='text' name='vsa_form_id' value='$form_id' />";
}

// Handle form submission and redirection
function vsa_handle_form_submission($fields, $entry, $form_data, $entry_id)
{
  $vsa_form_id = get_option('vsa_form_id');

  if ($form_data['id'] == $vsa_form_id) {
    wp_redirect(home_url('/virtual-staging-upload/'));
    exit;
  }
}
add_action('wpforms_process_complete', 'vsa_handle_form_submission', 10, 4);
