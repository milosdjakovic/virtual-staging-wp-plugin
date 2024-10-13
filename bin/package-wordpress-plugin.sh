#!/bin/bash

# Get the directory where the script is located
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# Navigate to the root directory of the project (parent of bin)
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"
# Change to the project root directory
cd "$PROJECT_ROOT" || exit

# Function to package a single plugin
package_plugin() {
  local plugin_dir="$1"
  local plugin_name=$(basename "$plugin_dir")
  local zip_file="${plugin_name}.zip"

  echo "Starting to package the $plugin_name plugin..."

  # Create the zip file
  (
    cd "$plugin_dir" && zip -r "$PROJECT_ROOT/$zip_file" . \
      -x "*.DS_Store" \
      -x "*/._*" \
      -x "*/__MACOSX/*" \
      -x "*.AppleDouble" \
      -x "*.LSOverride" \
      >/dev/null 2>&1
  )

  # Check if the zip was created successfully
  if [ $? -eq 0 ] && [ -f "$PROJECT_ROOT/$zip_file" ]; then
    echo "Packaging complete!"
    echo "Plugin has been packaged as $zip_file"
    echo "Location: $PROJECT_ROOT/$zip_file"
  else
    echo "Failed to package $plugin_name"
  fi
}

# Main execution
if [ $# -eq 0 ]; then
  # No arguments provided, package all plugins
  for plugin_dir in plugins/*; do
    if [ -d "$plugin_dir" ]; then
      package_plugin "$plugin_dir"
    fi
  done
else
  # Plugin name provided, package only that plugin
  PLUGIN_NAME="$1"
  PLUGIN_DIR="plugins/$PLUGIN_NAME"
  if [ -d "$PLUGIN_DIR" ]; then
    package_plugin "$PLUGIN_DIR"
  else
    echo "Error: Plugin directory not found: $PLUGIN_DIR"
    exit 1
  fi
fi
