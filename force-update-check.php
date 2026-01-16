<?php
/**
 * Force Update Check Script
 * 
 * Run this once to force WordPress to check for updates
 * Access: /wp-content/plugins/bp-dev-tools/force-update-check.php
 * Then delete this file.
 */

// Load WordPress
require_once('../../../wp-load.php');

// Check if user is admin
if (!current_user_can('manage_options')) {
    die('Permission denied');
}

// Delete update transients to force fresh check
delete_site_transient('update_plugins');
delete_transient('bp_dev_tools_update_check');

// Trigger update check
wp_update_plugins();

echo '<h1>✅ Update Check Forced</h1>';
echo '<p>Transients cleared. WordPress will check for updates now.</p>';
echo '<p><strong>Now:</strong></p>';
echo '<ol>';
echo '<li>Go to <a href="' . admin_url('plugins.php') . '">Plugins page</a></li>';
echo '<li>Or go to <a href="' . admin_url('admin.php?page=bp-dev-tools&tab=check-updates') . '">Check Updates tab</a> and click "Check Now"</li>';
echo '</ol>';
echo '<p><strong style="color:red;">⚠️ Delete this file after use!</strong></p>';
?>
