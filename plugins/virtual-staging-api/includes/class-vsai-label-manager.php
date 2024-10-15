<?php
// Exit if accessed directly
if (!defined('ABSPATH'))
  exit;

class VSAI_Label_Manager
{
  private static $label_substitutions = [
    'bed' => 'bedroom',
    // Add more substitutions here as needed
  ];

  public static function get_formatted_options($options)
  {
    $formatted_options = [];
    foreach ($options as $value) {
      $formatted_options[] = [
        'value' => $value,
        'label' => self::format_label($value)
      ];
    }
    return $formatted_options;
  }

  private static function format_label($value)
  {
    // First, check if there's a substitution
    if (isset(self::$label_substitutions[$value])) {
      $value = self::$label_substitutions[$value];
    }

    // Then, apply capitalization and replace underscores with spaces
    return ucwords(str_replace('_', ' ', $value));
  }
}
