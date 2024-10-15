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
        foreach ($forms as $index => $form) {
          $this->renderFormFields($index, $form);
        }
        ?>
      </div>
      <button type="button" class="button" id="vsa-add-form">Add Form Hook</button>
      <?php submit_button('Save All Changes'); ?>
    </form>

    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>
    <script>
      jQuery(document).ready(function ($) {
        tippy('.vsa-tooltip', {
          content: (reference) => reference.getAttribute('title'),
          allowHTML: true,
        });
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
                                                <button type="button" class="button vsa-remove-form">Remove Form Hook</button>
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

      .vsa-tooltip {
        cursor: help;
        color: #0073aa;
        margin-left: 5px;
      }

      .tippy-box {
        background-color: #333;
        color: #fff;
        border-radius: 4px;
        font-size: 14px;
      }
    </style>

    <?php
  }

  private function renderFormFields($index, $form)
  {
    ?>
    <div class="vsa-form-fields">
      <h3>Form Hook Configuration</h3>
      <input type="hidden" name="vsa_forms[<?php echo $index; ?>][id]" value="<?php echo $index; ?>">
      <p>
        <label for="form_id_<?php echo $index; ?>">Target Form ID:</label>
        <input type="text" id="form_id_<?php echo $index; ?>" name="vsa_forms[<?php echo $index; ?>][form_id]"
          value="<?php echo esc_attr($form['form_id'] ?? ''); ?>">
        <span class="vsa-tooltip"
          title="The ID of the WPForm to observe for successful submissions and trigger token generation.">?</span>
      </p>
      <p>
        <label for="renders_field_id_<?php echo $index; ?>">Renders Field ID:</label>
        <input type="text" id="renders_field_id_<?php echo $index; ?>"
          name="vsa_forms[<?php echo $index; ?>][renders_field_id]"
          value="<?php echo esc_attr($form['renders_field_id'] ?? ''); ?>">
        <span class="vsa-tooltip"
          title="The ID of the field within the target form that contains the number of renders for the authorization token.">?</span>
      </p>
      <p>
        <label for="renders_regex_<?php echo $index; ?>">Renders Extraction Regex:</label>
        <input type="text" id="renders_regex_<?php echo $index; ?>" name="vsa_forms[<?php echo $index; ?>][renders_regex]"
          value="<?php echo esc_attr($form['renders_regex'] ?? ''); ?>">
        <span class="vsa-tooltip"
          title="Optional regex to extract the number of renders from the field value. Leave empty if the field value is already a number.">?</span>
      </p>
      <p>
        <label for="redirect_path_<?php echo $index; ?>">Success Redirect Path:</label>
        <input type="text" id="redirect_path_<?php echo $index; ?>" name="vsa_forms[<?php echo $index; ?>][redirect_path]"
          value="<?php echo esc_attr($form['redirect_path'] ?? ''); ?>">
        <span class="vsa-tooltip"
          title="The path to redirect users after successful form submission and token generation.">?</span>
      </p>
      <button type="button" class="button vsa-remove-form">Remove Form Hook</button>
    </div>
    <?php
  }

}
