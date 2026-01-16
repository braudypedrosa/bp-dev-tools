# Build & Deployment Guide

Complete guide to building and deploying BP Dev Tools with Vue 3 + Vite + Tailwind CSS.

## 📋 Table of Contents

- [Prerequisites](#prerequisites)
- [Initial Setup](#initial-setup)
- [Build Process](#build-process)
- [Development Workflow](#development-workflow)
- [Production Build](#production-build)
- [Deployment](#deployment)
- [Troubleshooting](#troubleshooting)

## 🔧 Prerequisites

### Required Software

1. **Node.js** (v18 or higher)
   - If using **Local by Flywheel**, Node.js is included
   - Otherwise download: https://nodejs.org/
   ```bash
   node --version  # Check version
   ```

2. **npm** (comes with Node.js)
   ```bash
   npm --version  # Check version
   ```

## 🚀 Initial Setup

### Install Dependencies (First Time Only)

```bash
# Navigate to plugin directory
cd /path/to/wp-content/plugins/bp-dev-tools

# Install all dependencies
npm install
```

This installs:
- **Vue 3** - Progressive JavaScript framework
- **Vite** - Next generation frontend tooling
- **Tailwind CSS** - Utility-first CSS framework
- **Pinia** - Vue state management
- **Axios** - HTTP client for AJAX

**Note**: This creates a `node_modules/` folder (don't commit to Git!)

### That's It!

You're ready to build. Run `npm run build` or `npm run watch`.

## 🏗️ Build Process

### What Gets Built

The build process transforms your source files using Vite:

**Vue Components → Compiled JavaScript**
- `src/vue/**/*.vue` → `dist/js/admin.js`
- `src/js/admin-app.js` → Entry point

**Tailwind CSS → Compiled CSS**
- `src/css/app.css` → `dist/css/admin.css`
- Automatically purged of unused classes
- Optimized and minified

### Build Commands

```bash
# Production build (one-time)
npm run build

# Watch mode (auto-rebuild on file changes)
npm run watch
# or
npm start

# Using the build script
./build-now.sh          # One-time build
./build-now.sh --watch  # Watch mode
```

### Understanding the Build Process

#### 1. Vue Compilation

```
src/vue/App.vue + components
  ↓ Vite + Vue Plugin
  ↓ Component compilation
  ↓ Tree-shaking
  ↓ Minification (production)
  ↓ Source Maps (development)
dist/js/admin.js
```

#### 2. Tailwind CSS Processing

```
src/css/app.css
  ↓ PostCSS
  ↓ Tailwind CSS (JIT mode)
  ↓ Autoprefixer
  ↓ PurgeCSS (removes unused classes)
  ↓ Minification (production)
dist/css/admin.css
```

## 💻 Development Workflow

### Best Practice Workflow

1. **Start watch mode**:
   ```bash
   npm run watch
   # or
   ./build-now.sh --watch
   ```
   This watches for file changes and rebuilds automatically

2. **Edit source files**:
   - Edit `src/vue/**/*.vue` for Vue components
   - Edit `src/css/app.css` for styles
   - Save files

3. **Auto-rebuild**:
   - Files rebuild automatically on save (~1-2 seconds)
   - Refresh WordPress admin to see changes (Cmd+Shift+R)
   - Build errors show in terminal

4. **Stop watch mode**:
   - Press `Ctrl+C` in terminal

### Development Tips

- **Watch mode rebuilds quickly**: ~1-2 seconds per change
- **Browser DevTools**: Vue DevTools extension for debugging
- **Check terminal**: Watch for compilation errors
- **Hard refresh**: Use Cmd+Shift+R to clear cache
- **Production builds**: Always optimized and minified

### File Organization

```
Work in these files:              These are generated:
├── src/                          ├── dist/
│   ├── vue/                      │   ├── css/
│   │   ├── App.vue               │   │   └── admin.css
│   │   ├── components/           │   └── js/
│   │   ├── views/                │       ├── admin.js
│   │   └── store/                │       └── admin.js.map
│   ├── css/
│   │   └── app.css
│   └── js/
│       └── admin-app.js
```

**Never edit files in `dist/`** - they're overwritten on build!

### Component Structure

```
src/vue/
├── App.vue              # Root component
├── components/          # Reusable components
│   ├── AppHeader.vue
│   ├── Sidebar.vue
│   ├── ToolCard.vue
│   ├── ToolToggle.vue
│   ├── ToastContainer.vue
│   └── tools/          # Tool-specific components
│       └── SlugScannerTool.vue
├── views/              # Page components
│   ├── GeneralSettings.vue
│   └── ToolPage.vue
├── store/              # Pinia state management
│   └── tools.js
└── composables/        # Reusable composition functions
    └── useRoute.js
```

## 🚀 Production Build

### When to Use Production Build

- Before deploying to live site
- Before creating plugin ZIP
- For performance testing
- Before committing to Git

### Creating Production Build

```bash
npm run build
```

This creates optimized assets in `dist/` folder:
- Minified JavaScript
- Minified CSS with unused classes removed
- No source maps
- Optimized bundle sizes

### Production vs Development

| Feature | Development | Production |
|---------|------------|------------|
| CSS | Full Tailwind | Purged (~95% smaller) |
| JavaScript | Readable | Minified |
| Vue Devtools | Enabled | Disabled |
| Source Maps | Yes | Yes (for debugging) |
| File Size | Larger | ~70% smaller |
| Build Time | Instant (HMR) | ~2 seconds |
| Debugging | Easy | Possible with maps |

### Verify Production Build

```bash
# Check file sizes (should be small!)
ls -lh dist/css/
ls -lh dist/js/

# Expected sizes:
# admin.css: ~17 KB (gzipped: ~4 KB)
# admin.js: ~118 KB (gzipped: ~45 KB)
```

## 📦 Deployment

### Option 1: Deploy to Own Site

1. **Build for production**:
   ```bash
   npm run build
   ```

2. **Upload via FTP/SFTP**:
   - Upload entire plugin folder
   - Or use rsync (recommended)

3. **Using rsync** (recommended):
   ```bash
   rsync -avz --exclude 'node_modules' \
               --exclude '.git' \
               --exclude 'src' \
               ./ user@server:/path/to/wp-content/plugins/bp-dev-tools/
   ```

### Option 2: Git Deployment

1. **Build for production**:
   ```bash
   npm run build
   ```

2. **Commit compiled files**:
   ```bash
   git add dist/
   git commit -m "Build production assets"
   git push
   ```

3. **Pull on server**:
   ```bash
   # On server
   cd /path/to/plugins/bp-dev-tools
   git pull
   ```

### What to Include in Deployment

**Include**:
- ✅ `*.php` files
- ✅ `dist/` folder (compiled assets)
- ✅ `includes/` folder
- ✅ `templates/` folder
- ✅ `languages/` folder
- ✅ `README.md`

**Exclude**:
- ❌ `node_modules/` folder
- ❌ `src/` folder (source files)
- ❌ `.git/` folder
- ❌ `vite.config.js`
- ❌ `tailwind.config.js`
- ❌ `postcss.config.js`
- ❌ `package.json`, `package-lock.json`

### Deployment Checklist

- [ ] Run production build
- [ ] Test on local WordPress
- [ ] Check browser console for errors
- [ ] Verify all Vue components load
- [ ] Test tool toggle functionality
- [ ] Check responsive design
- [ ] Test on staging site
- [ ] Deploy to production
- [ ] Monitor browser console
- [ ] Check WordPress debug.log

## 🔍 Troubleshooting

### Build Fails

**Problem**: `npm install` fails

```bash
# Clear cache and reinstall
rm -rf node_modules package-lock.json
npm cache clean --force
npm install
```

**Problem**: Vite not found

```bash
# Use npx (always works)
npx vite build

# Or reinstall dependencies
rm -rf node_modules
npm install
```

**Problem**: Wrong Node version

```bash
# Check version (need 18+)
node --version

# Using Local by Flywheel? 
# Node is built-in, should work automatically
```

**Problem**: Vue compilation errors

- Check for syntax errors in `.vue` files
- Verify template, script, and style sections are properly closed
- Check for missing imports
- Look at terminal for specific error messages

**Problem**: Tailwind classes not working

- Check `tailwind.config.js` content paths
- Rebuild: `npm run build`
- Verify classes are used in Vue components
- Check for typos in class names

### Development Server Issues

**Problem**: Dev server won't start

```bash
# Check if port 3000 is already in use
lsof -ti:3000 | xargs kill -9

# Or change port in vite.config.js:
server: {
  port: 3001
}
```

**Problem**: HMR not working

```bash
# Solution: Restart dev server
# Press Ctrl+C
npm run dev
```

### WordPress Integration Issues

**Problem**: Vue app not mounting

1. Check browser console for errors
2. Verify `#bp-dev-tools-app` element exists
3. Check if JavaScript file is loading
4. Verify nonce is valid

**Problem**: AJAX requests failing

1. Check network tab in browser DevTools
2. Verify nonce is being sent correctly
3. Check WordPress `admin-ajax.php` URL
4. Look at PHP error logs

**Problem**: Blank page in WordPress admin

```bash
# Check WordPress debug mode
# In wp-config.php:
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);

# Check debug.log:
tail -f wp-content/debug.log
```

### Files Not Updating

**Problem**: Changes not appearing in WordPress

```bash
# Solution 1: Clear browser cache
# Press Ctrl+F5 (Windows) or Cmd+Shift+R (Mac)

# Solution 2: Rebuild
npm run build

# Solution 3: Clear WordPress cache
# (if using caching plugin)

# Solution 4: Check file timestamps
ls -lt dist/js/
ls -lt dist/css/
```

## 🎯 Advanced Topics

### Custom Vite Plugins

Add to `vite.config.js`:

```javascript
import customPlugin from 'vite-plugin-custom'

export default defineConfig({
  plugins: [
    vue(),
    customPlugin()
  ]
})
```

### Custom Tailwind Configuration

Edit `tailwind.config.js`:

```javascript
export default {
  theme: {
    extend: {
      colors: {
        'custom': '#ff00ff'
      }
    }
  }
}
```

### Adding New Tool Components

1. Create component in `src/vue/components/tools/`:
   ```vue
   <!-- NewTool.vue -->
   <template>
     <div>Your tool UI</div>
   </template>
   ```

2. Register in `src/vue/views/ToolPage.vue`:
   ```javascript
   import NewTool from '@components/tools/NewTool.vue'
   
   const toolComponents = {
     'new-tool': markRaw(NewTool)
   }
   ```

3. Add to PHP in `includes/admin/class-admin.php`:
   ```php
   $this->available_tools['new-tool'] = array(
     'id' => 'new-tool',
     'title' => 'New Tool',
     // ...
   );
   ```

## 📊 Build Performance

### Bundle Analysis

```bash
# Install bundle analyzer
npm install --save-dev rollup-plugin-visualizer

# Add to vite.config.js
import { visualizer } from 'rollup-plugin-visualizer'

export default defineConfig({
  plugins: [
    vue(),
    visualizer()
  ]
})

# Build and view
npm run build
open stats.html
```

### Optimize Bundle Size

1. **Tree shaking** (automatic in Vite)
2. **Code splitting** (automatic for async components)
3. **Compression** (enable gzip on server)
4. **Lazy loading** (use dynamic imports)

## 📚 Resources

- [Vite Documentation](https://vitejs.dev/)
- [Vue 3 Documentation](https://vuejs.org/)
- [Tailwind CSS Documentation](https://tailwindcss.com/)
- [Pinia Documentation](https://pinia.vuejs.org/)
- [Vue DevTools](https://devtools.vuejs.org/)

---

**Need more help?** Check the main [README.md](README.md) or [QUICKSTART.md](QUICKSTART.md).
