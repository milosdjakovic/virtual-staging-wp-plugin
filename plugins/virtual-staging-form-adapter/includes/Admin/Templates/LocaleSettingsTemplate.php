<?php

namespace VirtualStagingAdapter\Admin\Templates;

use VSAI_Language_Loader;

class LocaleSettingsTemplate {
    public function handleRequest() {
        if (isset($_POST['virtual_staging_locale'])) {
            VSAI_Language_Loader::set_locale($_POST['virtual_staging_locale']);
        }
    }

    public function render() {
        $current_locale = VSAI_Language_Loader::get_current_locale();
        ?>
        <div class="locale-settings-section">
            <h2>Language Settings</h2>
            <p>Current Locale: <?php echo esc_html($current_locale); ?></p>
            <form method="post">
                <table class="form-table">
                    <tr>
                        <th scope="row">Select Language</th>
                        <td>
                            <select name="virtual_staging_locale">
                                <?php foreach (glob(plugin_dir_path(VSAI_PLUGIN_FILE) . 'locale/*.json') as $file): ?>
                                    <?php $locale = basename($file); // Keep full filename with .json ?>
                                    <option value="<?php echo esc_attr($locale); ?>" 
                                            <?php selected($current_locale, $locale); ?>>
                                        <?php echo esc_html($locale); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                </table>
                <?php submit_button('Save Language'); ?>
            </form>
        </div>
        <?php
    }
}