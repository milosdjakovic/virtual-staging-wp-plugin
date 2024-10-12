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

# Create the zip file
(
  cd "$PLUGIN_DIR" && zip -r "$PROJECT_ROOT/$ZIP_FILE" . \
    -x "*.DS_Store" \
    -x "*/._*" \
    -x "*/__MACOSX/*" \
    -x "*.AppleDouble" \
    -x "*.LSOverride"
)

echo "Plugin has been packaged as $ZIP_FILE in the project root directory."
