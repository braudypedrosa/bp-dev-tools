# BP Dev Tools - Quick Start Guide

Welcome to BP Dev Tools! This guide will help you get started quickly.

## What Was Created

Your new BP Dev Tools plugin has been fully set up with:

✅ **Complete Plugin Structure**
- Main plugin file with proper naming and constants
- All classes updated with BP Dev Tools naming
- Database, installation, admin, and frontend classes
- Uninstall script for cleanup

✅ **Modern Admin Interface**
- Sidebar-based navigation system
- General Settings page with tool toggle grid
- Individual pages for each enabled tool
- Styled with Tailwind CSS (loaded via CDN)

✅ **Tool Management System**
- Enable/disable tools with toggle switches
- AJAX-powered (no page reload needed)
- Enabled tools automatically appear in sidebar
- Extensible architecture for adding custom tools

✅ **5 Pre-configured Tools** (ready for development)
- Code Snippets
- Query Monitor
- Debug Log Viewer
- Template Hints
- Hook Inspector

✅ **Assets & Styling**
- Custom CSS for WordPress admin integration
- JavaScript for AJAX and UI interactions
- Responsive design that works on all devices
- Toast notifications for user feedback

## Getting Started

### Step 1: Activate the Plugin

1. Go to **WordPress Admin** > **Plugins**
2. Find "BP Dev Tools" in the list
3. Click "Activate"
4. You'll see a success notice with a "Get Started" button

### Step 2: Access the Settings Page

There are three ways to access the settings:

1. Click the "Get Started" button in the activation notice
2. Go to **Dev Tools** in the admin menu (look for the wrench icon)
3. Click "Settings" link under the plugin name on the Plugins page

### Step 3: Enable Your Tools

1. You'll land on the **General Settings** page
2. You'll see a grid of available tools with descriptions
3. Toggle any tool ON to enable it
4. The page will reload and the tool will appear in the left sidebar
5. Click on the tool name in the sidebar to access its page

## How It Works

### Architecture Overview

```
┌─────────────────────────────────────────────┐
│           BP Dev Tools Header                │
│  ┌────────────────────────────────────────┐ │
│  │ Title: BP Dev Tools                    │ │
│  │ Subtitle: Manage your development tools│ │
│  │                          [X Tools Enabled]│
│  └────────────────────────────────────────┘ │
└─────────────────────────────────────────────┘
┌──────────┬──────────────────────────────────┐
│ SIDEBAR  │          CONTENT AREA            │
│          │                                  │
│ General  │  [Tool Cards Grid or Selected    │
│ Settings │   Tool Settings Page]            │
│ ──────── │                                  │
│ Enabled  │                                  │
│ Tools:   │                                  │
│          │                                  │
│ • Tool 1 │                                  │
│ • Tool 2 │                                  │
│ • Tool 3 │                                  │
│          │                                  │
└──────────┴──────────────────────────────────┘
```

### The Sidebar

**Always Visible:**
- 📋 General Settings (for enabling/disabling tools)

**Dynamic (appears when enabled):**
- Each enabled tool gets its own menu item
- Click to view that tool's settings/functionality
- Active page is highlighted in blue

### General Settings Page

Shows a **grid of tool cards**. Each card has:
- Tool icon (Dashicon)
- Tool name and description
- Toggle switch to enable/disable
- "Configure Tool →" link (when enabled)

### Individual Tool Pages

When you click on an enabled tool in the sidebar, you see:
- Tool header with icon and description
- Content area for tool functionality
- Currently shows "Under Development" placeholder
- Ready for you to add custom functionality

## Customizing Tools

### Adding Tool Functionality

Each tool has a dedicated action hook. Add your functionality like this:

```php
// In your theme's functions.php or a custom plugin
add_action( 'bp_dev_tools_render_tool_code-snippets', 'my_code_snippets_tool' );

function my_code_snippets_tool( $tool ) {
    ?>
    <div class="p-6">
        <h3 class="text-xl font-semibold mb-4">Code Snippets Manager</h3>
        
        <!-- Your tool's interface goes here -->
        <form method="post">
            <textarea name="code" class="w-full p-3 border rounded-lg" rows="10"></textarea>
            <button type="submit" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg">
                Save Snippet
            </button>
        </form>
    </div>
    <?php
}
```

### Adding New Tools

Add custom tools to the registry:

```php
add_filter( 'bp_dev_tools_available_tools', 'add_my_custom_tool' );

function add_my_custom_tool( $tools ) {
    $tools['my-tool'] = array(
        'id'          => 'my-tool',
        'title'       => 'My Custom Tool',
        'description' => 'This is my custom development tool',
        'icon'        => 'dashicons-hammer', // Any Dashicon
    );
    return $tools;
}
```

Then add its functionality:

```php
add_action( 'bp_dev_tools_render_tool_my-tool', 'render_my_custom_tool' );

function render_my_custom_tool( $tool ) {
    echo '<div>My tool content here</div>';
}
```

## Styling Tips

The interface uses **Tailwind CSS** utility classes. Common patterns:

```html
<!-- Card -->
<div class="bg-white rounded-lg shadow-sm p-6">...</div>

<!-- Button Primary -->
<button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
    Click Me
</button>

<!-- Button Secondary -->
<button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
    Cancel
</button>

<!-- Input Field -->
<input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200">

<!-- Grid Layout (2 columns) -->
<div class="grid grid-cols-2 gap-4">...</div>

<!-- Flex Layout (space between) -->
<div class="flex items-center justify-between">...</div>
```

## JavaScript API

Global object available: `bpDevToolsAdmin`

```javascript
// Show notification
BPDevToolsAdmin.showNotification('Success!', 'success');
BPDevToolsAdmin.showNotification('Error!', 'error');

// Confirm dialog
BPDevToolsAdmin.confirm('Are you sure?', function() {
    // User clicked OK
});

// Debounce function (for search inputs, etc.)
const debouncedSearch = BPDevToolsAdmin.debounce(function(query) {
    // Perform search
}, 300);
```

## File Structure Reference

```
bp-dev-tools/
├── bp-dev-tools.php              # ⭐ Main plugin file
├── includes/
│   ├── class-database.php        # Database operations
│   ├── class-install.php         # Activation/deactivation
│   ├── admin/
│   │   └── class-admin.php       # ⭐ Admin interface (edit this!)
│   └── frontend/
│       └── class-frontend.php    # Frontend functionality
├── dist/
│   ├── css/
│   │   ├── admin.css            # ⭐ Custom admin styles
│   │   └── frontend.css
│   └── js/
│       ├── admin.js             # ⭐ Admin JavaScript
│       └── frontend.js
├── src/                          # Source files (SCSS/JS)
├── templates/                    # Template files
├── languages/                    # Translation files
├── package.json                  # Node dependencies
├── gulpfile.js                   # Build scripts
├── webpack.config.js             # Webpack config
├── uninstall.php                 # Cleanup script
├── README.md                     # Full documentation
└── QUICKSTART.md                 # This file!
```

## Next Steps

### Development Path

1. **Choose a tool to implement first** (e.g., Debug Log Viewer)
2. **Hook into its render action** in your theme or a mu-plugin
3. **Build the tool's interface** using HTML/Tailwind CSS
4. **Add functionality** (forms, AJAX handlers, data processing)
5. **Test thoroughly** in the admin interface
6. **Repeat** for other tools

### Recommended First Tool

Start with **Debug Log Viewer** because it's straightforward:
1. Read `debug.log` file from `wp-content`
2. Display contents in a scrollable area
3. Add "Clear Log" button
4. Show file size and last modified date

## Need Help?

### Common Issues

**Tool toggle not working?**
- Check browser console for JavaScript errors
- Verify jQuery is loaded
- Check AJAX nonce is valid

**Styles look broken?**
- Verify Tailwind CSS CDN is loading
- Check for CSS conflicts with other plugins
- Clear browser cache

**Sidebar not updating?**
- Page should reload automatically after toggling
- If not, manually refresh the page
- Check AJAX response in browser network tab

### Development Mode

Enable WordPress debug mode in `wp-config.php`:

```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
define( 'SCRIPT_DEBUG', true );
```

This helps catch errors during development.

## Support

For questions or issues, check:
- Plugin README.md for detailed documentation
- WordPress Codex for WP functions
- Tailwind CSS docs for styling
- jQuery docs for JavaScript

---

**Happy Developing! 🚀**

Your BP Dev Tools plugin is ready to become your personal development toolkit!
