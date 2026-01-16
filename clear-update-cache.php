<?php
/**
 * Clear Update Checker Cache
 * 
 * This clears ALL caches related to plugin updates
 * Access: /wp-content/plugins/bp-dev-tools/clear-update-cache.php
 */

// Load WordPress
require_once('../../../wp-load.php');

// Check if user is admin
if (!current_user_can('manage_options')) {
    die('Permission denied');
}

echo '<h1>🧹 Clearing All Update Caches</h1>';

// 1. WordPress update transients
delete_site_transient('update_plugins');
echo '<p>✅ Deleted: update_plugins transient</p>';

// 2. Plugin Update Checker specific transients
delete_transient('bp_dev_tools_update_check');
echo '<p>✅ Deleted: bp_dev_tools_update_check transient</p>';

// 3. Look for any transients with 'puc' in the name (Plugin Update Checker)
global $wpdb;
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '%puc_%'");
echo '<p>✅ Deleted: All PUC transients</p>';

// 4. Look for any transients with 'bp-dev-tools' in the name
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '%bp-dev-tools%' AND option_name LIKE '%transient%'");
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '%bp_dev_tools%' AND option_name LIKE '%transient%'");
echo '<p>✅ Deleted: All bp-dev-tools transients</p>';

// 5. Trigger immediate update check
wp_update_plugins();
echo '<p>✅ Triggered: wp_update_plugins()</p>';

echo '<hr>';
echo '<h2>✨ Cache Cleared!</h2>';
echo '<p><strong>Now try:</strong></p>';
echo '<ol>';
echo '<li>Go to <a href="' . admin_url('admin.php?page=bp-dev-tools&tab=check-updates') . '">Check Updates tab</a></li>';
echo '<li>Click "Check Now" button</li>';
echo '<li>Should now detect v1.0.2!</li>';
echo '</ol>';
echo '<p style="color: red;"><strong>⚠️ Delete this file after use!</strong></p>';
?>
