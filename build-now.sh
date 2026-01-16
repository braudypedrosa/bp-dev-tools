#!/bin/bash

# Navigate to plugin directory
cd "$(dirname "$0")"

# Smart Node.js detection - use nvm version if available, otherwise system node
if [ -d "$HOME/.nvm/versions/node/v24.13.0" ]; then
  # Use the working nvm version
  export PATH="$HOME/.nvm/versions/node/v24.13.0/bin:$PATH"
  echo "Using Node.js v24.13.0 from nvm"
else
  echo "Using system Node.js: $(node --version)"
fi

# Check if watch mode is requested
if [ "$1" = "--watch" ] || [ "$1" = "-w" ]; then
  echo "🔄 Starting watch mode... (Press Ctrl+C to stop)"
  echo "Files will rebuild automatically when you save changes."
  echo ""
  npm run watch
else
  # Run one-time build
  echo "🔨 Building BP Dev Tools..."
  npm run build
  echo ""
  echo "✅ Build complete! Refresh your WordPress admin page to see changes."
fi
