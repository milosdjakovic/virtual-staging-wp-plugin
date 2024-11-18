<?php

namespace VirtualStagingAdapter\Admin\Templates;

use VSAI_Language_Loader;

class LanguageInfoTemplate
{
    public function render()
    {
        $current_locale = VSAI_Language_Loader::get_current_locale();
        ?>
        <div class="notice notice-info" style="padding-bottom: 14px;">
            <h2>Language Settings</h2>
            <p>Active language file: <strong><?php echo esc_html($current_locale); ?></strong></p>

            <form method="post">
                <select name="vsai_locale">
                    <?php foreach (glob(plugin_dir_path(VSAI_PLUGIN_FILE) . 'locale/*.json') as $file): ?>
                        <?php $locale = basename($file); ?>
                        <option value="<?php echo esc_attr($locale); ?>" <?php selected($current_locale, $locale); ?>>
                            <?php echo esc_html($locale); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php submit_button('Save Language', 'secondary', 'submit', false); ?>
            </form>
        </div>
        <?php
    }

    public function handleRequest()
    {
        if (isset($_POST['vsai_locale'])) {
            if (false !== get_option(VSAI_Language_Loader::OPTION_KEY)) {
                update_option(VSAI_Language_Loader::OPTION_KEY, sanitize_text_field($_POST['vsai_locale']));
            }
        }
    }
}