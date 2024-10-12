# Virtual Staging

## Form Shortcodes

### Upload Form

Use this shortcode for the upload form:

```php
[vsai_template type="upload" next_page_url="/virtual-staging-main"]
```

`next_page_url`: Path to the "main" page after clicking "Process Image" button.

Main Form
Use this shortcode for the main form:

```php
[vsai_template type="main" next_page_url="/virtual-staging-upload"]
```

next_page_url: Path to the "upload" form when "Upload Another Image" button is clicked.

### Generating Authorization Token

The endpoint to generate the authorization token is:

```plaintext
https://your-domain-here.com/wp-json/vsai/v1/generate-token
```
