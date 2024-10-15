<?php

namespace VirtualStagingAdapter\Admin\Templates;

use VirtualStagingAdapter\Admin\SettingsManager;

class SettingsTemplate
{
  private $settingsManager;

  public function __construct(SettingsManager $settingsManager)
  {
    $this->settingsManager = $settingsManager;
  }

  public function render()
  {
    $forms = $this->settingsManager->getForms();
    ?>
    <h2>WPForm Configuration</h2>
    <p>This section configures the integration with the WPForms plugin. It determines which forms trigger the redirection
      and where users are sent after successful submission.</p>
    <form method="post" action="options.php" id="vsa-settings-form">
      <?php settings_fields('vsa_settings'); ?>
      <div id="vsa-forms-container">
        <?php
        if (empty($forms)) {
          $this->renderFormFields();
        } else {
          foreach ($forms as $index => $form) {
            $this->renderFormFields($index, $form);
          }
        }
        ?>
      </div>
      <button type="button" class="button" id="vsa-add-form">Add Form</button>
      <?php submit_button('Save All Changes'); ?>
    </form>

    <script>
      jQuery(document).ready(function ($) {
        let formIndex = <?php echo count($forms); ?>;

        $('#vsa-add-form').on('click', function () {
          const newForm = `
                            <div class="vsa-form-fields">
                                <h3>Form Configuration</h3>
                                <input type="hidden" name="vsa_forms[${formIndex}][id]" value="${formIndex}">
                                <p>
                                    <label>Form ID:</label>
                                    <input type="text" name="vsa_forms[${formIndex}][form_id]" value="">
                                </p>
                                <p>
                                    <label>Renders Field ID:</label>
                                    <input type="text" name="vsa_forms[${formIndex}][renders_field_id]" value="">
                                </p>
                                <p>
                                    <label>Renders Regex:</label>
                                    <input type="text" name="vsa_forms[${formIndex}][renders_regex]" value="">
                                </p>
                                <p>
                                    <label>Redirect Path:</label>
                                    <input type="text" name="vsa_forms[${formIndex}][redirect_path]" value="">
                                </p>
                                <button type="button" class="button vsa-remove-form">Remove Form</button>
                            </div>
                        `;
          $('#vsa-forms-container').append(newForm);
          formIndex++;
        });

        $(document).on('click', '.vsa-remove-form', function () {
          $(this).closest('.vsa-form-fields').remove();
        });
      });
    </script>
    <style>
      .vsa-form-fields {
        background: #fff;
        border: 1px solid #ccc;
        padding: 10px;
        margin-bottom: 10px;
      }

      .vsa-form-fields label {
        display: inline-block;
        width: 150px;
      }
    </style>
    <?php
  }

  private function renderFormFields($index = 0, $form = [])
  {
    ?>
    <div class="vsa-form-fields">
      <h3>Form Configuration</h3>
      <input type="hidden" name="vsa_forms[<?php echo $index; ?>][id]" value="<?php echo $index; ?>">
      <p>
        <label>Form ID:</label>
        <input type="text" name="vsa_forms[<?php echo $index; ?>][form_id]"
          value="<?php echo esc_attr($form['form_id'] ?? ''); ?>">
      </p>
      <p>
        <label>Renders Field ID:</label>
        <input type="text" name="vsa_forms[<?php echo $index; ?>][renders_field_id]"
          value="<?php echo esc_attr($form['renders_field_id'] ?? ''); ?>">
      </p>
      <p>
        <label>Renders Regex:</label>
        <input type="text" name="vsa_forms[<?php echo $index; ?>][renders_regex]"
          value="<?php echo esc_attr($form['renders_regex'] ?? ''); ?>">
      </p>
      <p>
        <label>Redirect Path:</label>
        <input type="text" name="vsa_forms[<?php echo $index; ?>][redirect_path]"
          value="<?php echo esc_attr($form['redirect_path'] ?? ''); ?>">
      </p>
      <?php if ($index > 0 || !empty($form)): ?>
        <button type="button" class="button vsa-remove-form">Remove Form</button>
      <?php endif; ?>
    </div>
    <?php
  }
}
