# Development Quick Start

Simple workflow for developing BP Dev Tools with Vue 3.

## ⚡ Quick Commands

### One-time Build
```bash
./build-now.sh
# or
npm run build
```

### Watch Mode (Recommended)
```bash
./watch.sh
# or
./build-now.sh --watch
```

## 🔄 Development Workflow

1. **Start watch mode**: `npm run watch`
2. **Edit files** in `src/vue/`, `src/css/`
3. **Save** - builds automatically (~1-2 seconds)
4. **Refresh WordPress** admin page (Cmd+Shift+R)
5. **Repeat!**

## 📁 File Structure

```
src/
├── vue/              # Vue components
│   ├── App.vue       # Root component
│   ├── components/   # Reusable components
│   ├── views/        # Page components
│   └── store/        # Pinia state
├── css/              # Tailwind CSS
└── js/               # Entry point

dist/                 # ← WordPress loads from here
├── css/admin.css
└── js/admin.js
```

## 🎨 Making Changes

### Update a Component
1. Edit `src/vue/components/ComponentName.vue`
2. Save file
3. Wait for build (~1-2 sec)
4. Refresh WordPress admin

### Update Styles
1. Edit `src/css/app.css` (Tailwind utilities)
2. Or edit Vue component `<style>` blocks
3. Save and rebuild

### Add a New Tool
See BUILD.md → "Adding a New Tool"

## 🐛 Troubleshooting

**Changes not showing?**
- Hard refresh: `Cmd+Shift+R` (Mac) or `Ctrl+F5` (Windows)
- Check terminal for build errors
- Verify watch mode is running

**Build fails?**
- Check Node version: `node --version` (need 18+)
- Reinstall: `rm -rf node_modules && npm install`
- Using Local WP? Node is built-in and should work

## 📚 Full Documentation

- [BUILD.md](BUILD.md) - Complete build guide
- [README.md](README.md) - Project overview
- [QUICKSTART.md](QUICKSTART.md) - Setup guide

---

**That's it! Just `npm run watch`, edit, save, refresh. Simple!** 🚀
