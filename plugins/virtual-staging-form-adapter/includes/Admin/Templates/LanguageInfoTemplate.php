<?php

namespace VirtualStagingAdapter\Admin\Templates;

use VSAI_Language_Loader;

class LanguageInfoTemplate
{
    public function render()
    {
        ?>
        <div class="notice notice-info">
            <p>Active language file from database: <strong><?php echo esc_html(VSAI_Language_Loader::get_current_locale()); ?></strong></p>
        </div>
        <?php
    }
}