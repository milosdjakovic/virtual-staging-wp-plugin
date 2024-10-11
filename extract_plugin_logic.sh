#!/bin/bash

# Set the output file
output_file="virtual_staging_api_plugin_logic.md"

# Clear or create the output file
>"$output_file"

# Add the heading
echo "# Virtual Staging API Plugin Logic" >>"$output_file"
echo "" >>"$output_file"

# Function to process each file
process_file() {
  local file="$1"
  local extension="${file##*.}"

  # Write the file path
  echo "$file" >>"$output_file"
  echo "" >>"$output_file"

  # Write the opening code block with file type
  echo "\`\`\`$extension" >>"$output_file"

  # Write file contents, trimming trailing whitespace and empty lines
  sed -e 's/[[:space:]]*$//' "$file" | sed -e :a -e '/^\n*$/{$d;N;ba' -e '}' >>"$output_file"

  # Write the closing code block on a new line
  echo -e "\n\`\`\`" >>"$output_file"

  # Add a newline after the code block
  echo "" >>"$output_file"
}

# Find and process PHP, JS, YML, and INI files
find . -type f \( -name "*.php" -o -name "*.js" -o -name "*.yml" -o -name "*.ini" \) | sort | while read -r file; do
  process_file "$file"
done

echo "Plugin logic has been extracted into $output_file"
