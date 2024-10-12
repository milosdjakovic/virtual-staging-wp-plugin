#!/bin/bash

# Get the directory where the script is located
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# Navigate to the root directory of the project (parent of bin)
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"

# Change to the project root directory
cd "$PROJECT_ROOT" || exit

# Name of the plugin directory
PLUGIN_DIR="plugins/virtual-staging-api"

# Name of the output zip file
ZIP_FILE="virtual-staging-api.zip"

echo "Starting to package the Virtual Staging API plugin..."

# Create the zip file
(
  cd "$PLUGIN_DIR" && zip -r "$PROJECT_ROOT/$ZIP_FILE" . \
    -x "*.DS_Store" \
    -x "*/._*" \
    -x "*/__MACOSX/*" \
    -x "*.AppleDouble" \
    -x "*.LSOverride" \
    >/dev/null 2>&1
)

# Check if the zip was created successfully
if [ $? -eq 0 ] && [ -f "$PROJECT_ROOT/$ZIP_FILE" ]; then
  echo "Packaging complete!"
  echo "Plugin has been packaged as $ZIP_FILE"
  echo "Location: $PROJECT_ROOT/$ZIP_FILE"
else
  echo "Error: Packaging failed. Please check for any issues."
  exit 1
fi
