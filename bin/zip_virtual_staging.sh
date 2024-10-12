#!/usr/bin/env bash

# Set the root directory (one level up from the bin directory)
ROOT_DIR="$(dirname "$(dirname "$0")")"

# Set the source directory and destination
SOURCE_DIR="$ROOT_DIR/plugins/virtual-staging-api"
DEST_ZIP="$ROOT_DIR/virtual-staging-api.zip"

# Check if the source directory exists
if [ ! -d "$SOURCE_DIR" ]; then
  echo "Error: Source directory '$SOURCE_DIR' not found."
  exit 1
fi

# Create the zip file
echo "Creating zip file..."
(cd "$ROOT_DIR/plugins" && zip -r "$DEST_ZIP" virtual-staging-api -x "*.DS_Store" -x "**/.DS_Store" -x "**/__MACOSX/*")

# Check if the zip was created successfully
if [ $? -eq 0 ]; then
  echo "Successfully created virtual-staging-api.zip in the project root."
  echo "Zip file contents:"
  unzip -l "$DEST_ZIP"
else
  echo "Error: Failed to create zip file."
  exit 1
fi
