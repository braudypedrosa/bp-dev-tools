#!/bin/bash

###############################################################################
# BP Dev Tools - Plugin Packager
# Creates a production-ready WordPress plugin ZIP file
###############################################################################

set -e  # Exit on error

# Colors for output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Plugin details
PLUGIN_SLUG="bp-dev-tools"
PLUGIN_VERSION=$(grep "Version:" bp-dev-tools.php | head -1 | sed 's/.*Version:[[:space:]]*//' | sed 's/[[:space:]]*$//')
BUILD_DIR="build"
RELEASE_DIR="releases"
TEMP_DIR="$BUILD_DIR/$PLUGIN_SLUG"

echo -e "${BLUE}========================================${NC}"
echo -e "${BLUE}BP Dev Tools - Plugin Packager${NC}"
echo -e "${BLUE}========================================${NC}"
echo ""

# Clean up old build
echo -e "${YELLOW}→${NC} Cleaning up old builds..."
rm -rf "$BUILD_DIR"
mkdir -p "$BUILD_DIR"
mkdir -p "$RELEASE_DIR"

# Build assets
echo -e "${YELLOW}→${NC} Building production assets..."
npm run build

# Create temp directory
echo -e "${YELLOW}→${NC} Creating plugin package..."
mkdir -p "$TEMP_DIR"

# Copy plugin files (production only)
echo -e "${YELLOW}→${NC} Copying plugin files..."

# Main plugin files
cp bp-dev-tools.php "$TEMP_DIR/"
cp uninstall.php "$TEMP_DIR/"
cp README.md "$TEMP_DIR/"

# Includes directory (PHP classes)
cp -r includes "$TEMP_DIR/"

# Vendor directory (Composer dependencies)
if [ -d "vendor" ]; then
  cp -r vendor "$TEMP_DIR/"
fi

# Compiled assets only
cp -r dist "$TEMP_DIR/"

# Languages directory
cp -r languages "$TEMP_DIR/"

# Templates directory (if exists)
if [ -d "templates" ]; then
  cp -r templates "$TEMP_DIR/"
fi

# Create ZIP file
ZIP_NAME="$PLUGIN_SLUG-v$PLUGIN_VERSION.zip"
echo -e "${YELLOW}→${NC} Creating ZIP: $ZIP_NAME"

cd "$BUILD_DIR"
zip -r "../$RELEASE_DIR/$ZIP_NAME" "$PLUGIN_SLUG" -q

cd ..

# Cleanup
echo -e "${YELLOW}→${NC} Cleaning up temporary files..."
rm -rf "$BUILD_DIR"

# Success message
echo ""
echo -e "${GREEN}✓ Success!${NC}"
echo -e "${GREEN}========================================${NC}"
echo -e "Plugin packaged: ${GREEN}$RELEASE_DIR/$ZIP_NAME${NC}"
echo -e "Version: ${GREEN}$PLUGIN_VERSION${NC}"
echo -e "Size: ${GREEN}$(du -h "$RELEASE_DIR/$ZIP_NAME" | cut -f1)${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""
echo -e "Ready to install on WordPress! 🎉"
echo ""
