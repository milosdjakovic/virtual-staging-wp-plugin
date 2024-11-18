#!/usr/bin/env bash

# Get the root directory (one level up from the bin directory)
ROOT_DIR="$(dirname "$(dirname "$0")")"
PLUGINS_DIR="$ROOT_DIR/plugins"
EXPORTS_DIR="$ROOT_DIR/code-exports"

# Create exports directory if it doesn't exist
mkdir -p "$EXPORTS_DIR"

# Function to process each file
process_file() {
  local file="$1"
  local output_file="$2"
  local relative_path="${file#$ROOT_DIR/}"
  local extension="${file##*.}"

  # Write the file path as H3
  echo "### $relative_path" >>"$output_file"
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

# Function to process a single plugin
process_plugin() {
  local plugin_name="$1"
  local output_file="$2"

  # Add the plugin heading
  echo "## $plugin_name" >>"$output_file"
  echo "" >>"$output_file"

  # Process root config files
  root_files=(".gitignore" "docker-compose.yml" "uploads.ini")
  for file in "${root_files[@]}"; do
    if [ -f "$ROOT_DIR/$file" ]; then
      process_file "$ROOT_DIR/$file" "$output_file"
    fi
  done

  # Find and process PHP and JS files in the plugin directory
  plugin_dir="$PLUGINS_DIR/$plugin_name"
  if [ -d "$plugin_dir" ]; then
    find "$plugin_dir" -type f \( -name "*.php" -o -name "*.js" \) | sort | while read -r file; do
      process_file "$file" "$output_file"
    done
  else
    echo "Warning: Plugin directory not found: $plugin_dir"
  fi
}

# Main execution
if [ $# -eq 0 ]; then
  # No arguments provided, process all plugins
  output_file="$EXPORTS_DIR/plugins_code.md"
  >"$output_file"
  echo "# All Plugins Code" >>"$output_file"
  echo "" >>"$output_file"

  for plugin_dir in "$PLUGINS_DIR"/*; do
    if [ -d "$plugin_dir" ]; then
      plugin_name=$(basename "$plugin_dir")
      process_plugin "$plugin_name" "$output_file"
    fi
  done
else
  # Plugin name provided
  PLUGIN_NAME="$1"
  output_file="$EXPORTS_DIR/${PLUGIN_NAME}_code.md"
  >"$output_file"
  echo "# $PLUGIN_NAME Plugin Code" >>"$output_file"
  echo "" >>"$output_file"
  process_plugin "$PLUGIN_NAME" "$output_file"
fi

echo "Plugin code has been extracted into $output_file"
