<?php
// Exit if accessed directly
if (!defined('ABSPATH'))
  exit;

class VSAI_Label_Manager
{
    public static function get_formatted_options($options, $translations, $type)
    {
        $formatted_options = [];
        foreach ($options as $value) {
            $label = isset($translations['select_options'][$type][$value])
                ? $translations['select_options'][$type][$value]
                : ucwords(str_replace('_', ' ', $value));

            $formatted_options[] = [
                'value' => $value,
                'label' => $label
            ];
        }
        return $formatted_options;
    }
}
