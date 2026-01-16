<?php
/**
 * Installation Handler Class
 *
 * Handles plugin activation, deactivation, and uninstallation.
 * This class manages database table creation, default settings, and cleanup.
 *
 * @package BP_Dev_Tools
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class BP_Plugin_Boilerplate_Install
 *
 * Manages the installation and uninstallation process of the plugin.
 * Handles database table creation during activation and cleanup during
 * deactivation/uninstallation.
 *
 * BEGINNER'S NOTE:
 * - Activation: Runs once when the plugin is activated (first turned on)
 * - Deactivation: Runs when the plugin is deactivated (turned off)
 * - Uninstallation: Runs when the plugin is deleted from WordPress
 * - Best practice: Don't delete data on deactivation, only on uninstall
 *
 * @since 1.0.0
 */
class BP_Dev_Tools_Install {

	/**
	 * Plugin activation hook.
	 *
	 * Called when the plugin is activated. This method:
	 * - Creates custom database tables (if needed)
	 * - Sets default options/settings
	 * - Flushes rewrite rules if custom post types or endpoints are registered
	 * - Schedules any cron jobs
	 * - Sets up initial data or configurations
	 *
	 * BEGINNER'S NOTE:
	 * - This runs only once when activating the plugin
	 * - It's registered in the main plugin file using register_activation_hook()
	 * - Keep this method fast - don't perform heavy operations here
	 * - Use transients to display one-time messages after activation
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function activate() {
		// Create database tables (if your plugin needs custom tables).
		self::create_tables();

		// Set default options and settings.
		self::set_default_options();

		// Set activation flag to show welcome message.
		// This transient expires after 60 seconds or is manually deleted after being shown.
		set_transient( 'bp_dev_tools_activated', true, 60 );

		// Flush rewrite rules (only needed if you register custom post types or rewrite rules).
		// This ensures WordPress knows about any custom URLs your plugin creates.
		flush_rewrite_rules();

		/**
		 * Fires after the plugin has been activated.
		 *
		 * Allows other plugins or themes to hook into your plugin's activation.
		 *
		 * @since 1.0.0
		 */
		do_action( 'bp_dev_tools_activated' );
	}

	/**
	 * Plugin deactivation hook.
	 *
	 * Called when the plugin is deactivated. This method:
	 * - Clears scheduled cron jobs
	 * - Flushes rewrite rules
	 * - Does NOT delete data (only on uninstall)
	 * - Cleans up temporary data
	 *
	 * IMPORTANT: We don't delete tables or options here. Data is only removed during
	 * uninstallation to prevent accidental data loss if user deactivates temporarily.
	 *
	 * BEGINNER'S NOTE:
	 * - Users often deactivate plugins temporarily for troubleshooting
	 * - Never delete user data on deactivation
	 * - Only clean up temporary/cached data that can be regenerated
	 * - wp_next_scheduled() checks if a cron job is scheduled
	 * - wp_unschedule_event() removes a scheduled cron job
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function deactivate() {
		// Clear scheduled cron jobs (if any).
		// Example: If you schedule a daily cleanup task, remove it on deactivation.
		$timestamp = wp_next_scheduled( 'bp_dev_tools_daily_task' );
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, 'bp_dev_tools_daily_task' );
		}

		// Flush rewrite rules to clean up any custom URLs.
		flush_rewrite_rules();

		// Clear any cached data.
		// Example: Delete transients or cached results.
		// delete_transient( 'bp_dev_tools_cache' );

		/**
		 * Fires after the plugin has been deactivated.
		 *
		 * Allows other plugins or themes to hook into your plugin's deactivation.
		 *
		 * @since 1.0.0
		 */
		do_action( 'bp_dev_tools_deactivated' );
	}

	/**
	 * Create database tables.
	 *
	 * Creates all required custom database tables for the plugin.
	 * This is called during plugin activation.
	 *
	 * BEGINNER'S NOTE:
	 * - We load the database class and call its create_tables() method
	 * - This keeps database logic centralized in one place
	 * - If you don't need custom tables, you can leave this empty
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private static function create_tables() {
		require_once BP_DEV_TOOLS_INCLUDES_DIR . 'class-database.php';
		
		$database = new BP_Dev_Tools_Database();
		$database->create_tables();
	}

	/**
	 * Set default plugin options.
	 *
	 * Creates default settings in the WordPress options table.
	 * These can be modified later through the admin interface.
	 *
	 * BEGINNER'S NOTE:
	 * - Options are stored in the wp_options table
	 * - add_option() only creates the option if it doesn't exist
	 * - update_option() creates or updates the option
	 * - get_option() retrieves the option value
	 * - Use arrays to group related settings together
	 *
	 * Common setting types:
	 * - Boolean (true/false): Enable/disable features
	 * - Strings: Text values like titles or API keys
	 * - Arrays: Multiple related settings grouped together
	 * - Integers: Numeric values like limits or counts
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private static function set_default_options() {
		$default_options = array(
			// General settings
			'enabled'                    => true,
			'debug_mode'                 => false,
			
			// Enabled tools (empty by default, user selects which tools to enable)
			'enabled_tools'              => array(),
		);

		// Only add option if it doesn't exist (won't overwrite existing settings).
		// This is important if the user already has settings and reactivates the plugin.
		if ( ! get_option( 'bp_dev_tools_settings' ) ) {
			add_option( 'bp_dev_tools_settings', $default_options );
		}

		// Store installation date for future reference.
		// current_time( 'mysql' ) returns current time in MySQL format (YYYY-MM-DD HH:MM:SS).
		if ( ! get_option( 'bp_dev_tools_installed_date' ) ) {
			add_option( 'bp_dev_tools_installed_date', current_time( 'mysql' ) );
		}

		// Store plugin version for tracking and migrations.
		update_option( 'bp_dev_tools_version', BP_DEV_TOOLS_VERSION );
	}

	/**
	 * Plugin uninstallation (called from uninstall.php).
	 *
	 * Removes all plugin data from the database including:
	 * - Custom tables
	 * - Plugin options
	 * - Transients (temporary cached data)
	 * - User meta data related to the plugin
	 * - Post meta data related to the plugin
	 *
	 * This should only be called from the uninstall.php file.
	 *
	 * BEGINNER'S NOTE:
	 * - This is the ONLY place where you should delete user data
	 * - Always give users an option to keep their data on uninstall
	 * - Delete options using delete_option()
	 * - Delete transients using delete_transient()
	 * - Drop tables using the database class
	 *
	 * BEST PRACTICE:
	 * - Check if user wants to keep data (via a setting)
	 * - Log what's being deleted (if debug mode is on)
	 * - Use action hooks to allow cleanup of related data
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function uninstall() {
		// Get settings to check if user wants to keep data.
		$settings = get_option( 'bp_dev_tools_settings', array() );
		$keep_data = isset( $settings['keep_data_on_uninstall'] ) ? $settings['keep_data_on_uninstall'] : false;

		// Only delete data if user hasn't chosen to keep it.
		if ( ! $keep_data ) {
			// Drop database tables.
			require_once BP_DEV_TOOLS_INCLUDES_DIR . 'class-database.php';
			$database = new BP_Dev_Tools_Database();
			$database->drop_tables();

			// Delete all plugin options.
			delete_option( 'bp_dev_tools_settings' );
			delete_option( 'bp_dev_tools_version' );
			delete_option( 'bp_dev_tools_db_version' );
			delete_option( 'bp_dev_tools_installed_date' );

			// Clear any transients (temporary cached data).
			delete_transient( 'bp_dev_tools_activated' );
			delete_transient( 'bp_dev_tools_cache' );

			// Delete user meta data (if your plugin stores user-specific data).
			// Example: delete_metadata( 'user', 0, 'bp_dev_tools_user_pref', '', true );

			// Delete post meta data (if your plugin stores post-specific data).
			// Example: delete_post_meta_by_key( 'bp_dev_tools_meta' );

			/**
			 * Fires after the plugin data has been deleted during uninstall.
			 *
			 * Allows other plugins or custom code to clean up related data.
			 *
			 * @since 1.0.0
			 */
			do_action( 'bp_dev_tools_uninstalled' );
		}
	}

	/**
	 * Check if the plugin needs a database update.
	 *
	 * Compares the installed version with the current version
	 * to determine if database migrations or updates are needed.
	 *
	 * BEGINNER'S NOTE:
	 * - version_compare() compares two version strings (e.g., '1.0.0' vs '1.1.0')
	 * - The third parameter ('<') checks if first version is less than second
	 * - This is useful when you need to migrate data between plugin versions
	 *
	 * Example usage:
	 * if ( self::needs_database_update() ) {
	 *     self::run_database_migration();
	 * }
	 *
	 * @since 1.0.0
	 * @return bool True if update needed, false otherwise.
	 */
	public static function needs_database_update() {
		$installed_version = get_option( 'bp_dev_tools_version', '0.0.0' );
		return version_compare( $installed_version, BP_DEV_TOOLS_VERSION, '<' );
	}

	/**
	 * Run database migration/update.
	 *
	 * Performs necessary database changes when updating from an older version.
	 * This is called automatically if needs_database_update() returns true.
	 *
	 * BEGINNER'S NOTE:
	 * - Database migrations update your database structure when plugin updates
	 * - Each version that needs changes should have its own condition
	 * - Always test migrations thoroughly before release
	 * - Consider backing up data before major structure changes
	 *
	 * Example: If upgrading from 1.0.0 to 1.1.0 added a new table column,
	 * you would add that column here for users upgrading from 1.0.0.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function run_database_migration() {
		$installed_version = get_option( 'bp_dev_tools_version', '0.0.0' );

		// Example: Migrate from version 1.0.0 to 1.1.0
		// if ( version_compare( $installed_version, '1.1.0', '<' ) ) {
		//     // Perform database changes for 1.1.0
		//     // Example: Add new column, update existing data, etc.
		// }

		// Example: Migrate from version 1.1.0 to 1.2.0
		// if ( version_compare( $installed_version, '1.2.0', '<' ) ) {
		//     // Perform database changes for 1.2.0
		// }

		// Update version number after successful migration.
		update_option( 'bp_dev_tools_version', BP_DEV_TOOLS_VERSION );
	}
}
