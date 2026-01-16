#!/bin/bash

# Navigate to plugin directory
cd "$(dirname "$0")"

# Smart Node.js detection - use nvm version if available, otherwise system node
if [ -d "$HOME/.nvm/versions/node/v24.13.0" ]; then
  # Use the working nvm version
  export PATH="$HOME/.nvm/versions/node/v24.13.0/bin:$PATH"
  echo "✅ Using Node.js v24.13.0 from nvm"
else
  echo "ℹ️  Using system Node.js: $(node --version)"
fi

echo ""
echo "🔄 Starting watch mode..."
echo "Files will rebuild automatically when you save changes."
echo "Press Ctrl+C to stop."
echo ""

# Run watch mode
npm run watch
