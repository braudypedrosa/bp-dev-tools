<?php
/**
 * Uninstall Script
 *
 * Fired when the plugin is uninstalled via WordPress admin.
 * This file is called automatically by WordPress during plugin deletion.
 *
 * BEGINNER'S NOTE:
 * - This runs ONLY when the plugin is deleted (not deactivated)
 * - WordPress automatically calls this file if it exists
 * - Use this to clean up database tables, options, and plugin data
 * - NEVER delete data on deactivation - only on uninstall
 * - Always give users an option to keep their data
 *
 * Security:
 * - Check WP_UNINSTALL_PLUGIN constant to ensure this is a legitimate uninstall
 * - Don't rely on admin authentication checks here
 * - WordPress handles security before calling this file
 *
 * @package BP_Dev_Tools
 * @since 1.0.0
 */

// Exit if accessed directly or not from WordPress.
// This security check ensures the file is only run during WordPress uninstall process.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Load plugin file to access constants and classes.
// We need this to use our plugin's classes and constants.
require_once plugin_dir_path( __FILE__ ) . 'bp-dev-tools.php';

/**
 * Clean up plugin data.
 *
 * This calls the uninstall method from the install class
 * which handles database cleanup and option removal.
 *
 * BEGINNER'S NOTE:
 * - The Install class has a dedicated uninstall() method
 * - That method checks if user wants to keep data
 * - It removes tables, options, transients, and meta data
 * - This keeps uninstall logic organized in one place
 */
BP_Dev_Tools_Install::uninstall();

/**
 * Alternative: Direct cleanup (if not using Install class)
 * 
 * Uncomment this section if you prefer to handle cleanup directly here
 * instead of using the Install class method.
 */
/*
global $wpdb;

// Check if user wants to keep data
$settings = get_option( 'bp_dev_tools_settings', array() );
$keep_data = isset( $settings['keep_data_on_uninstall'] ) ? $settings['keep_data_on_uninstall'] : false;

if ( ! $keep_data ) {
	// Delete custom database tables
	$table_name = $wpdb->prefix . 'bp_dev_tools_main';
	$wpdb->query( "DROP TABLE IF EXISTS {$table_name}" );

	// Delete all plugin options
	delete_option( 'bp_dev_tools_settings' );
	delete_option( 'bp_dev_tools_version' );
	delete_option( 'bp_dev_tools_db_version' );
	delete_option( 'bp_dev_tools_installed_date' );

	// Delete all transients
	delete_transient( 'bp_dev_tools_activated' );
	delete_transient( 'bp_dev_tools_cache' );

	// Delete all user meta
	delete_metadata( 'user', 0, 'bp_dev_tools_user_pref', '', true );

	// Delete all post meta
	delete_post_meta_by_key( 'bp_dev_tools_meta' );

	// For multisite: Delete site options and user meta across network
	if ( is_multisite() ) {
		delete_site_option( 'bp_dev_tools_network_settings' );
		
		// Clean up for each site in the network
		$sites = get_sites( array( 'number' => 0 ) );
		foreach ( $sites as $site ) {
			switch_to_blog( $site->blog_id );
			
			// Repeat deletions for each site
			delete_option( 'bp_dev_tools_settings' );
			// ... other options
			
			restore_current_blog();
		}
	}
}
*/

/**
 * Cleanup checklist for your plugin:
 *
 * [ ] Custom database tables (if any)
 * [ ] Plugin options from wp_options table
 * [ ] Plugin transients (temporary cached data)
 * [ ] User meta data related to plugin
 * [ ] Post meta data related to plugin
 * [ ] Term meta data (if using custom taxonomies)
 * [ ] Custom post types and taxonomies
 * [ ] Scheduled cron jobs
 * [ ] Uploaded files in wp-content/uploads/
 * [ ] Custom capabilities added to roles
 * [ ] Site options (for multisite)
 * [ ] Custom directory (if plugin created one)
 */
