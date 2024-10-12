<?php

class VSAI_Token_Handler
{
  private $table_name;

  public function __construct()
  {
    global $wpdb;
    $this->table_name = $wpdb->prefix . 'vsai_tokens';
  }

  public function initialize()
  {
    if (!$this->table_exists()) {
      $this->create_table();
    }
  }

  private function table_exists()
  {
    global $wpdb;
    $query = $wpdb->prepare("SHOW TABLES LIKE %s", $this->table_name);
    return $wpdb->get_var($query) === $this->table_name;
  }

  private function create_table()
  {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $this->table_name (
          id mediumint(9) NOT NULL AUTO_INCREMENT,
          token varchar(255) NOT NULL,
          image_upload_count int(11) NOT NULL DEFAULT 0,
          image_upload_limit int(11) NOT NULL DEFAULT 5,
          created_at datetime DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY  (id),
          UNIQUE KEY token (token)
      ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    if (!empty($wpdb->last_error)) {
      error_log('VSAI Token Handler: Error creating table: ' . $wpdb->last_error);
    } else {
      error_log('VSAI Token Handler: Table created successfully.');
    }
  }

  public function add_token($limit = 5, $max_attempts = 10)
  {
    global $wpdb;

    for ($attempt = 0; $attempt < $max_attempts; $attempt++) {
      $token = bin2hex(random_bytes(32)); // 64 character token

      // Check if the token already exists
      $exists = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$this->table_name} WHERE token = %s",
        $token
      ));

      if ($exists == 0) {
        // Token is unique, insert it
        $result = $wpdb->insert(
          $this->table_name,
          array(
            'token' => $token,
            'image_upload_count' => 0,
            'image_upload_limit' => $limit
          ),
          array('%s', '%d', '%d')
        );

        if ($result === false) {
          return false; // Insert failed
        }

        return $token; // Successfully inserted
      }
    }

    // If we've reached here, we've failed to generate a unique token after max_attempts
    return false;
  }

  public function token_exists($token)
  {
    global $wpdb;
    $query = $wpdb->prepare("SELECT COUNT(*) FROM $this->table_name WHERE token = %s", $token);
    return (int) $wpdb->get_var($query) > 0;
  }

  public function is_limit_breached($token)
  {
    global $wpdb;
    $query = $wpdb->prepare(
      "SELECT image_upload_count >= image_upload_limit AS limit_breached 
           FROM $this->table_name 
           WHERE token = %s",
      $token
    );
    return (bool) $wpdb->get_var($query);
  }

  public function increment_count($token)
  {
    global $wpdb;
    $result = $wpdb->query($wpdb->prepare(
      "UPDATE $this->table_name 
           SET image_upload_count = image_upload_count + 1 
           WHERE token = %s",
      $token
    ));
    return $result !== false;
  }

  public function get_token_status($token)
  {
    global $wpdb;
    $query = $wpdb->prepare(
      "SELECT image_upload_limit, image_upload_count, (image_upload_limit - image_upload_count) AS uploads_left
             FROM {$this->table_name}
             WHERE token = %s",
      $token
    );
    $result = $wpdb->get_row($query);

    if (!$result) {
      return null; // Token not found
    }

    return array(
      'limit' => (int) $result->image_upload_limit,
      'count' => (int) $result->image_upload_count,
      'uploads_left' => (int) $result->uploads_left
    );
  }
}
