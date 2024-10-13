#!/usr/bin/env bash

# Check if plugin name is provided
if [ $# -eq 0 ]; then
  echo "Error: Plugin name is required."
  echo "Usage: $0 <plugin_name>"
  exit 1
fi

PLUGIN_NAME="$1"

# Get the root directory (one level up from the bin directory)
ROOT_DIR="$(dirname "$(dirname "$0")")"

# Set the output file in the root directory
output_file="$ROOT_DIR/${PLUGIN_NAME}_code.md"

# Clear or create the output file
>"$output_file"

# Add the heading
echo "# $PLUGIN_NAME Plugin Code" >>"$output_file"
echo "" >>"$output_file"

# Function to process each file
process_file() {
  local file="$1"
  local relative_path="${file#$ROOT_DIR/}"
  local extension="${file##*.}"

  # Write the file path
  echo "## $relative_path" >>"$output_file"
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

# Process root config files
root_files=(".gitignore" "docker-compose.yml" "uploads.ini")
for file in "${root_files[@]}"; do
  if [ -f "$ROOT_DIR/$file" ]; then
    process_file "$ROOT_DIR/$file"
  fi
done

# Find and process PHP and JS files in the plugin directory
plugin_dir="$ROOT_DIR/plugins/$PLUGIN_NAME"
if [ -d "$plugin_dir" ]; then
  find "$plugin_dir" -type f \( -name "*.php" -o -name "*.js" \) | sort | while read -r file; do
    process_file "$file"
  done
else
  echo "Error: Plugin directory not found: $plugin_dir"
  exit 1
fi

echo "Plugin code has been extracted into $output_file"
