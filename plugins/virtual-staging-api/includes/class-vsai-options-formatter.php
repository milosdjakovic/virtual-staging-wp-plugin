<?php
// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

class VSAI_Options_Formatter {
    private $translations;

    public function __construct($translations) {
        $this->translations = $translations;
    }

    public function format($options, $type) {
        return VSAI_Label_Manager::get_formatted_options($options, $this->translations, $type);
    }

    public function to_html($formatted_options) {
        $html = '';
        foreach ($formatted_options as $option) {
            $html .= sprintf(
                '<option value="%s">%s</option>',
                esc_attr($option['value']),
                esc_html($option['label'])
            );
        }
        return $html;
    }
}