<?php
/**
 * Debug Update Checker
 * 
 * Upload this file to your WordPress root and visit:
 * http://yoursite.local/debug-update-checker.php
 * 
 * This will show detailed information about the update checker.
 */

// Load WordPress
require_once __DIR__ . '/wp-load.php';

// Must be admin
if (!current_user_can('manage_options')) {
    die('Access denied');
}

echo '<h1>BP Dev Tools - Update Checker Debug</h1>';
echo '<style>body { font-family: monospace; padding: 20px; } pre { background: #f5f5f5; padding: 15px; overflow-x: auto; }</style>';

// Check if plugin is active
echo '<h2>1. Plugin Status</h2>';
$plugin_file = 'bp-dev-tools/bp-dev-tools.php';
$is_active = is_plugin_active($plugin_file);
echo '<pre>';
echo 'Plugin Active: ' . ($is_active ? 'YES' : 'NO') . "\n";
echo 'Plugin File: ' . $plugin_file . "\n";
echo '</pre>';

// Check if autoloader exists
echo '<h2>2. Composer Autoloader</h2>';
$autoloader_path = WP_CONTENT_DIR . '/plugins/bp-dev-tools/vendor/autoload.php';
echo '<pre>';
echo 'Autoloader Path: ' . $autoloader_path . "\n";
echo 'Autoloader Exists: ' . (file_exists($autoloader_path) ? 'YES' : 'NO') . "\n";
echo '</pre>';

// Load autoloader
if (file_exists($autoloader_path)) {
    require_once $autoloader_path;
}

// Check if PUC class exists
echo '<h2>3. Plugin Update Checker Library</h2>';
echo '<pre>';
echo 'PucFactory Class Exists: ' . (class_exists('YahnisElsts\PluginUpdateChecker\v5\PucFactory') ? 'YES' : 'NO') . "\n";
echo '</pre>';

// Try to initialize update checker
echo '<h2>4. Initialize Update Checker</h2>';
echo '<pre>';
try {
    if (class_exists('YahnisElsts\PluginUpdateChecker\v5\PucFactory')) {
        $update_checker = YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
            'https://github.com/braudypedrosa/bp-dev-tools',
            WP_CONTENT_DIR . '/plugins/bp-dev-tools/bp-dev-tools.php',
            'bp-dev-tools'
        );
        
        echo "✓ Update checker created successfully\n\n";
        
        // Get current version
        echo "Current Plugin Version: " . $update_checker->getInstalledVersion() . "\n";
        
        // Force check for updates
        echo "\nForcing update check...\n";
        $update_checker->checkForUpdates();
        
        $update_state = $update_checker->getUpdate();
        
        if ($update_state) {
            echo "\n✓ UPDATE AVAILABLE!\n";
            echo "Latest Version: " . $update_state->version . "\n";
            echo "Download URL: " . $update_state->download_url . "\n";
            echo "Release Notes:\n" . substr($update_state->changelog, 0, 500) . "\n";
        } else {
            echo "\n✗ No update available\n";
            echo "\nChecking GitHub API directly...\n";
            
            // Check GitHub API
            $github_api_url = 'https://api.github.com/repos/braudypedrosa/bp-dev-tools/releases/latest';
            $response = wp_remote_get($github_api_url);
            
            if (!is_wp_error($response)) {
                $body = wp_remote_retrieve_body($response);
                $data = json_decode($body);
                
                echo "\nGitHub API Response:\n";
                echo "Latest Release Tag: " . ($data->tag_name ?? 'NOT FOUND') . "\n";
                echo "Release Name: " . ($data->name ?? 'NOT FOUND') . "\n";
                echo "Published: " . ($data->published_at ?? 'NOT FOUND') . "\n";
                
                if (isset($data->assets) && !empty($data->assets)) {
                    echo "\nRelease Assets:\n";
                    foreach ($data->assets as $asset) {
                        echo "  - " . $asset->name . " (" . $asset->size . " bytes)\n";
                        echo "    Download: " . $asset->browser_download_url . "\n";
                    }
                } else {
                    echo "\n✗ NO ASSETS FOUND IN RELEASE!\n";
                }
            } else {
                echo "\n✗ GitHub API Error: " . $response->get_error_message() . "\n";
            }
        }
        
    } else {
        echo "✗ PucFactory class not found\n";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
echo '</pre>';

// Check WordPress transients
echo '<h2>5. WordPress Update Transients</h2>';
echo '<pre>';
$update_plugins = get_site_transient('update_plugins');
if ($update_plugins && isset($update_plugins->response['bp-dev-tools/bp-dev-tools.php'])) {
    echo "WordPress sees update available:\n";
    print_r($update_plugins->response['bp-dev-tools/bp-dev-tools.php']);
} else {
    echo "No update registered in WordPress transients\n";
}
echo '</pre>';

// Check WP-Cron schedule
echo '<h2>6. WP-Cron Schedule</h2>';
echo '<pre>';
$cron_array = _get_cron_array();
$next_update_check = wp_next_scheduled('wp_update_plugins');
if ($next_update_check) {
    echo "Next automatic update check: " . date('Y-m-d H:i:s', $next_update_check) . "\n";
    echo "Time until next check: " . human_time_diff($next_update_check) . "\n\n";
} else {
    echo "❌ No update check scheduled!\n\n";
}

// Check if WP-Cron is working
if (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON) {
    echo "⚠️  WP_CRON is DISABLED in wp-config.php\n";
    echo "You'll need to set up a system cron job for automatic updates.\n";
} else {
    echo "✓ WP_CRON is enabled (automatic updates will work)\n";
}

// Check last update check
if (isset($update_plugins->last_checked)) {
    echo "\nLast update check: " . date('Y-m-d H:i:s', $update_plugins->last_checked) . "\n";
    echo "Time since last check: " . human_time_diff($update_plugins->last_checked) . " ago\n";
}
echo '</pre>';

echo '<hr>';
echo '<p><a href="' . admin_url('admin.php?page=bp-dev-tools&tab=check-updates') . '">← Back to Check Updates</a></p>';
