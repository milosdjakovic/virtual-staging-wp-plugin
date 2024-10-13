# Virtual Staging API Form Adapter - Configuration Guide

## Form ID

Enter the WPForms Form ID that triggers the redirection.
Example: `1234`

## Renders Field ID

Input the ID of the form field containing the number of renders.
Example: `5` (if it's the 5th field in your form)

## Renders Regex

Enter a regular expression to extract the number of renders from the field value.
Example: `/^(\d+)\s+IMAGES/`
This regex works for values like "5 IMAGES - $25.00" or "9 IMAGES - $45.00"

## Redirect Path

Specify the path where users will be redirected after form submission.
Start with a forward slash.
Example: `/virtual-staging-upload/`

Note: Ensure your regex matches the actual format of your form field values.
The extracted number will be added as the 'at' parameter in the redirect URL.
