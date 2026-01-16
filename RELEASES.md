# Creating Plugin Releases

This guide explains how to create new releases for BP Dev Tools that will automatically update via the built-in update checker.

## 📋 Release Process Overview

1. **Update version number** in plugin files
2. **Build and test** the plugin locally
3. **Commit changes** to Git
4. **Create GitHub release** with version tag
5. **Automatic updates** work immediately!

---

## Step 1: Update Version Numbers

Update the version in these files:

### **bp-dev-tools.php** (Main Plugin File)
```php
/**
 * Version: 1.1.0  👈 Update this
 */

const VERSION = '1.1.0';  👈 And this
```

### Make Your Changes
- Add new features
- Fix bugs
- Update documentation

---

## Step 2: Build the Plugin

Run the build command to compile assets:

```bash
npm run build
```

Test locally to ensure everything works correctly.

---

## Step 3: Commit to Git

```bash
git add -A
git commit -m "Release v1.1.0 - Add new features"
git push origin master
```

---

## Step 4: Create GitHub Release

### **Option A: Using GitHub Web Interface** (Recommended)

1. Go to your repository: https://github.com/braudypedrosa/bp-dev-tools
2. Click **"Releases"** in the right sidebar
3. Click **"Create a new release"**
4. Fill in the details:
   - **Tag version:** `v1.1.0` (must start with 'v' and match your plugin version)
   - **Release title:** `Version 1.1.0` or describe the update
   - **Description:** List changes, new features, bug fixes
5. Click **"Publish release"**

### **Option B: Using GitHub CLI**

```bash
# Create a release with the packaged plugin
gh release create v1.1.0 \
  --title "Version 1.1.0" \
  --notes "Release notes here" \
  releases/bp-dev-tools-v1.1.0.zip
```

---

## Step 5: Automatic Updates

Once you create a GitHub release:

✅ **WordPress will automatically detect the update** within 12 hours  
✅ **Users see update notification** in WordPress admin  
✅ **One-click update** from the Plugins page  
✅ **Check Updates tab** shows the new version immediately

---

## 📝 Release Checklist

Before creating a release, make sure:

- [ ] Version numbers updated in `bp-dev-tools.php`
- [ ] `VERSION` constant updated
- [ ] All changes committed to Git
- [ ] Plugin built (`npm run build`)
- [ ] Tested locally
- [ ] Tagged with matching version (e.g., `v1.1.0`)
- [ ] Release notes written
- [ ] GitHub release created

---

## 🎯 Version Naming

**Follow semantic versioning:**

- **Major:** `2.0.0` - Breaking changes
- **Minor:** `1.1.0` - New features, backward compatible
- **Patch:** `1.0.1` - Bug fixes

**Tag format:**
- ✅ `v1.1.0` (with 'v' prefix)
- ✅ `1.1.0` (without prefix also works)
- ❌ `version-1.1.0` (don't use this)

---

## 🔄 How Updates Work

1. **Plugin Update Checker** checks GitHub every 12 hours
2. Looks for **GitHub releases** with version tags
3. Compares latest release version with installed version
4. Shows **update notification** if newer version exists
5. Downloads and installs **from GitHub release**

---

## 🛠️ Testing Updates

### **Before Publishing:**

1. Create a **draft release** first
2. Test on a staging site
3. Verify the update process works
4. Publish the release when ready

### **Force Manual Check:**

Go to: **BP Dev Tools → Check Updates → Check Now**

---

## 📦 Including Release Assets

If you want to include a pre-packaged ZIP:

1. Run: `npm run package`
2. Upload `releases/bp-dev-tools-v1.1.0.zip` to the GitHub release
3. The update checker will use this ZIP file

**Note:** The packager automatically includes everything needed:
- Compiled assets (`dist/`)
- PHP files (`includes/`, `bp-dev-tools.php`)
- Composer dependencies (`vendor/`)
- Languages

---

## 🚨 Common Issues

### **Update Not Showing?**

- Check version numbers match
- Ensure tag starts with 'v' (e.g., `v1.1.0`)
- Wait 12 hours or force check in Check Updates tab
- Verify release is published (not draft)

### **Update Fails?**

- Check file permissions
- Verify GitHub repository is accessible
- Ensure ZIP structure is correct
- Check WordPress error log

---

## 📚 Resources

- [GitHub Releases Documentation](https://docs.github.com/en/repositories/releasing-projects-on-github/managing-releases-in-a-repository)
- [Plugin Update Checker Library](https://github.com/YahnisElsts/plugin-update-checker)
- [Semantic Versioning](https://semver.org/)

---

## Example Release Notes Template

```markdown
## What's New in v1.1.0

### 🎉 New Features
- Added new Slug Scanner tool
- Automatic GitHub updates

### 🐛 Bug Fixes
- Fixed styling issues in admin interface

### 🔧 Improvements
- Updated to Vue 3 + Vite architecture
- Improved build process

### 📦 Installation
Download and install from WordPress Plugins page or manually upload the ZIP file.
```

---

**Happy Releasing! 🚀**
