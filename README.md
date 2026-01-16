# BP Dev Tools

A modern, Vue-powered directory of developer tools for WordPress development.

## Description

BP Dev Tools provides a centralized dashboard for managing various development utilities in WordPress. Built with Vue 3, Vite, and Tailwind CSS, the plugin features a modern, reactive interface where you can enable/disable tools and access their functionality.

### Key Features

- **Modern Tech Stack**: Built with Vue 3 + Vite + Tailwind CSS
- **Reactive UI**: Fast, responsive interface with hot module replacement
- **Modular Tool System**: Enable only the tools you need
- **Sidebar Navigation**: Clean, organized interface with left sidebar navigation
- **State Management**: Pinia-powered state management for seamless data flow
- **AJAX-Powered**: Smooth, no-reload tool toggling
- **Extensible Architecture**: Easy to add custom tools via hooks and Vue components
- **WordPress Standards**: Follows WP coding standards and best practices
- **Production-Ready**: Optimized builds with tree-shaking and code splitting

## Available Tools

The plugin currently includes:

1. **Slug Scanner** - Scan and analyze post slugs across your WordPress site for optimization and SEO purposes _(Coming Soon)_

More tools will be added based on development needs!

## Tech Stack

### Frontend
- **Vue 3** - Progressive JavaScript framework with Composition API
- **Vite** - Next generation frontend tooling for blazing fast HMR
- **Tailwind CSS** - Utility-first CSS framework (properly integrated, not CDN)
- **Pinia** - Intuitive Vue state management
- **Axios** - Promise-based HTTP client

### Backend
- **WordPress** - PHP-based CMS
- **WordPress AJAX API** - For communication between Vue and PHP
- **WordPress Settings API** - For storing plugin settings

### Build Tools
- **Vite** - Module bundler and dev server
- **PostCSS** - CSS transformations
- **Autoprefixer** - Automatic vendor prefixing
- **Terser** - JavaScript minification

## Installation

### From Source

1. Upload the `bp-dev-tools` folder to `/wp-content/plugins/`
2. Navigate to the plugin directory:
   ```bash
   cd /wp-content/plugins/bp-dev-tools
   ```
3. Install dependencies:
   ```bash
   npm install
   ```
4. Build production assets:
   ```bash
   npm run build
   ```
5. Activate the plugin through 'Plugins' in WordPress
6. Navigate to 'Dev Tools' in the admin menu

### For Development

1. Clone or download the plugin
2. Install dependencies:
   ```bash
   npm install
   ```
3. Start watch mode:
   ```bash
   npm run watch
   ```
4. Activate plugin in WordPress
5. Edit Vue components, save, refresh WordPress!

## Usage

### Enabling Tools

1. Go to **Dev Tools** > **General Settings**
2. You'll see a grid of available tools (3-column layout)
3. Toggle the switch for any tool to enable/disable it
4. Enabled tools will automatically appear in the left sidebar
5. Click on any enabled tool to access its settings and functionality

### Accessing Tool Settings

Once a tool is enabled:

1. It appears in the left sidebar navigation
2. Click on the tool name to open its settings page
3. The tool's interface loads in the main content area
4. All interactions are reactive and instant

### User Interface

The plugin uses a sidebar-based layout:

- **Header**: Shows plugin name and enabled tool count
- **Left Sidebar**: Navigation menu with General Settings + enabled tools
- **Main Content**: Active page content (tool interfaces)
- **Toast Notifications**: Success/error messages for actions

## Development

### Project Structure

```
bp-dev-tools/
├── bp-dev-tools.php           # Main plugin file
├── includes/
│   ├── admin/
│   │   └── class-admin.php    # WordPress admin integration
│   ├── class-database.php     # Database handler
│   └── class-install.php      # Installation handler
├── src/                       # Source files (not deployed)
│   ├── vue/                   # Vue components
│   │   ├── App.vue            # Root component
│   │   ├── components/        # Reusable components
│   │   │   ├── AppHeader.vue
│   │   │   ├── Sidebar.vue
│   │   │   ├── ToolCard.vue
│   │   │   ├── ToolToggle.vue
│   │   │   ├── ToastContainer.vue
│   │   │   └── tools/        # Tool-specific components
│   │   ├── views/            # Page components
│   │   │   ├── GeneralSettings.vue
│   │   │   └── ToolPage.vue
│   │   ├── store/            # Pinia stores
│   │   │   └── tools.js
│   │   └── composables/      # Reusable composition functions
│   ├── css/
│   │   └── app.css           # Tailwind CSS entry
│   └── js/
│       └── admin-app.js      # Vue app entry point
├── dist/                     # Compiled assets (deployed)
│   ├── css/
│   │   └── admin.css         # Compiled Tailwind CSS
│   └── js/
│       └── admin.js          # Compiled Vue app
├── vite.config.js            # Vite configuration
├── tailwind.config.js        # Tailwind configuration
├── postcss.config.js         # PostCSS configuration
└── package.json              # Dependencies and scripts
```

### Adding a New Tool

#### 1. Register in PHP

Edit `includes/admin/class-admin.php`:

```php
private function register_available_tools() {
    $this->available_tools = array(
        'your-tool' => array(
            'id'          => 'your-tool',
            'title'       => __( 'Your Tool', 'bp-dev-tools' ),
            'description' => __( 'Description of your tool.', 'bp-dev-tools' ),
            'icon'        => 'dashicons-admin-generic',
        ),
    );
}
```

#### 2. Create Vue Component

Create `src/vue/components/tools/YourTool.vue`:

```vue
<template>
  <div class="your-tool">
    <h3>Your Tool Interface</h3>
    <!-- Your tool's UI here -->
  </div>
</template>

<script setup>
import { ref } from 'vue'

defineProps({
  tool: {
    type: Object,
    required: true
  }
})

// Your tool's logic here
</script>
```

#### 3. Register Component

Edit `src/vue/views/ToolPage.vue`:

```javascript
import YourTool from '@components/tools/YourTool.vue'

const toolComponents = {
  'your-tool': markRaw(YourTool),
  // ... other tools
}
```

#### 4. Build and Test

```bash
npm run build
```

That's it! Your tool will now appear in the General Settings and can be toggled on/off.

### Build Commands

```bash
# Development server with HMR
npm run dev

# Production build
npm run build

# Preview production build
npm run preview
```

### Coding Standards

- **PHP**: WordPress Coding Standards
- **JavaScript**: ESLint + Vue style guide
- **CSS**: Tailwind utility classes
- **Vue**: Composition API with `<script setup>`
- **State**: Pinia stores for shared state
- **Naming**: kebab-case for files, PascalCase for components

## Hooks & Filters

### PHP Hooks

```php
// Add custom tools
add_filter( 'bp_dev_tools_available_tools', function( $tools ) {
    $tools['custom-tool'] = array(
        'id'          => 'custom-tool',
        'title'       => 'Custom Tool',
        'description' => 'Your custom tool',
        'icon'        => 'dashicons-admin-generic',
    );
    return $tools;
});
```

## Requirements

- **WordPress**: 5.0 or higher
- **PHP**: 7.4 or higher
- **Node.js**: 18 or higher (for development)
- **Modern Browser**: Chrome, Firefox, Safari, Edge (latest versions)

## Browser Support

- Chrome (last 2 versions)
- Firefox (last 2 versions)
- Safari (last 2 versions)
- Edge (last 2 versions)

## Performance

- **Initial Load**: ~160 KB (JS + CSS, uncompressed)
- **Gzipped**: ~49 KB
- **First Paint**: < 100ms
- **Vue Reactivity**: < 50ms for state changes
- **Build Time**: ~2 seconds (production)
- **HMR**: < 100ms (development)

## Changelog

### 1.0.0
- Initial release
- Vue 3 + Vite + Tailwind CSS architecture
- Slug Scanner tool (coming soon)
- Modern reactive UI
- Settings management
- Tool enable/disable system

## Contributing

Contributions are welcome! Please follow these guidelines:

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Write/update tests if applicable
5. Update documentation
6. Submit a pull request

## License

GPL-2.0-or-later

## Credits

- Built with [Vue 3](https://vuejs.org/)
- Styled with [Tailwind CSS](https://tailwindcss.com/)
- Powered by [Vite](https://vitejs.dev/)
- Icons from [WordPress Dashicons](https://developer.wordpress.org/resource/dashicons/)

## Support

For issues, questions, or feature requests, please open an issue on GitHub.

## Author

Your Name

## Links

- [Documentation](BUILD.md)
- [Quick Start Guide](QUICKSTART.md)
- [WordPress Plugin Repository](#) _(Coming Soon)_
