<?php
/**
 * Plugin Name: BP Dev Tools
 * Plugin URI: https://github.com/braudyp/bp-dev-tools
 * Description: A comprehensive directory of developer tools with an extensible settings interface for managing various development utilities.
 * Version: 1.0.0
 * Author: Braudy Pedrosa
 * Author URI: https://braudyp.dev/
 * Text Domain: bp-dev-tools
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package BP_Dev_Tools
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin class - BP_Dev_Tools
 * 
 * This is the core class that initializes the plugin and manages all its components.
 * It uses a singleton pattern to ensure only one instance exists throughout the request lifecycle.
 *
 * The singleton pattern prevents multiple instances from being created, which could lead to
 * duplicate hook registrations and unexpected behavior.
 *
 * @since 1.0.0
 */
final class BP_Dev_Tools {

	/**
	 * Plugin version.
	 *
	 * Used for cache busting assets and tracking plugin updates.
	 *
	 * @var string
	 */
	const VERSION = '1.0.0';

	/**
	 * Minimum PHP version required.
	 *
	 * Set this to match your plugin's requirements.
	 *
	 * @var string
	 */
	const MIN_PHP_VERSION = '7.4';

	/**
	 * Minimum WordPress version required.
	 *
	 * Set this to match your plugin's requirements.
	 *
	 * @var string
	 */
	const MIN_WP_VERSION = '5.8';

	/**
	 * The single instance of the class.
	 *
	 * This variable holds the singleton instance to ensure
	 * only one instance of the plugin is active at a time.
	 *
	 * @var BP_Dev_Tools
	 */
	protected static $instance = null;

	/**
	 * Database handler instance.
	 *
	 * Manages all database operations including table creation,
	 * queries, and database maintenance.
	 *
	 * @var BP_Dev_Tools_Database
	 */
	public $database;

	/**
	 * Admin handler instance.
	 *
	 * Manages the WordPress admin interface including settings pages,
	 * admin notices, and backend functionality.
	 *
	 * @var BP_Dev_Tools_Admin
	 */
	public $admin;

	/**
	 * Main BP_Dev_Tools Instance.
	 *
	 * Ensures only one instance of BP_Dev_Tools is loaded or can be loaded.
	 * This static method returns the singleton instance, creating it if it doesn't exist yet.
	 *
	 * @since 1.0.0
	 * @static
	 * @return BP_Dev_Tools - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * BP_Dev_Tools Constructor.
	 *
	 * Sets up the plugin by defining constants, checking requirements,
	 * and initializing hooks and includes.
	 *
	 * This is a private constructor to enforce the singleton pattern.
	 * Use the instance() method to get the plugin instance.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		$this->define_constants();
		
		// Check if requirements are met before proceeding.
		if ( ! $this->check_requirements() ) {
			return;
		}

		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Define plugin constants.
	 *
	 * Sets up all the constants used throughout the plugin for paths,
	 * URLs, and other configuration values. Constants are used instead of
	 * variables for values that don't change during execution.
	 *
	 * @since 1.0.0
	 */
	private function define_constants() {
		$this->define( 'BP_DEV_TOOLS_VERSION', self::VERSION );
		$this->define( 'BP_DEV_TOOLS_PLUGIN_FILE', __FILE__ );
		$this->define( 'BP_DEV_TOOLS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
		$this->define( 'BP_DEV_TOOLS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		$this->define( 'BP_DEV_TOOLS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		$this->define( 'BP_DEV_TOOLS_INCLUDES_DIR', BP_DEV_TOOLS_PLUGIN_DIR . 'includes/' );
		$this->define( 'BP_DEV_TOOLS_ASSETS_URL', BP_DEV_TOOLS_PLUGIN_URL . 'dist/' );
	}

	/**
	 * Define constant if not already set.
	 *
	 * This helper method checks if a constant exists before defining it,
	 * allowing other code to override constants if needed.
	 *
	 * @since 1.0.0
	 * @param string $name  Constant name.
	 * @param mixed  $value Constant value.
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Check if system requirements are met.
	 *
	 * Verifies that the server is running the minimum required versions
	 * of PHP and WordPress. If requirements aren't met, admin notices are
	 * displayed and the plugin won't initialize.
	 *
	 * @since 1.0.0
	 * @return bool True if requirements met, false otherwise.
	 */
	private function check_requirements() {
		global $wp_version;

		// Check PHP version.
		if ( version_compare( PHP_VERSION, self::MIN_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', array( $this, 'php_version_notice' ) );
			return false;
		}

		// Check WordPress version.
		if ( version_compare( $wp_version, self::MIN_WP_VERSION, '<' ) ) {
			add_action( 'admin_notices', array( $this, 'wp_version_notice' ) );
			return false;
		}

		return true;
	}

	/**
	 * Display PHP version notice.
	 *
	 * Shows an admin notice when the PHP version doesn't meet requirements.
	 * The notice is dismissible and only shows in the admin area.
	 *
	 * @since 1.0.0
	 */
	public function php_version_notice() {
		?>
		<div class="notice notice-error">
			<p>
				<?php
				printf(
					/* translators: 1: Required PHP version, 2: Current PHP version */
					esc_html__( 'BP Dev Tools requires PHP version %1$s or higher. You are running version %2$s.', 'bp-dev-tools' ),
					esc_html( self::MIN_PHP_VERSION ),
					esc_html( PHP_VERSION )
				);
				?>
			</p>
		</div>
		<?php
	}

	/**
	 * Display WordPress version notice.
	 *
	 * Shows an admin notice when the WordPress version doesn't meet requirements.
	 * The notice is dismissible and only shows in the admin area.
	 *
	 * @since 1.0.0
	 */
	public function wp_version_notice() {
		global $wp_version;
		?>
		<div class="notice notice-error">
			<p>
				<?php
				printf(
					/* translators: 1: Required WordPress version, 2: Current WordPress version */
					esc_html__( 'BP Dev Tools requires WordPress version %1$s or higher. You are running version %2$s.', 'bp-dev-tools' ),
					esc_html( self::MIN_WP_VERSION ),
					esc_html( $wp_version )
				);
				?>
			</p>
		</div>
		<?php
	}

	/**
	 * Include required files.
	 *
	 * Loads all the necessary PHP files for the plugin to function.
	 * This includes classes for database management, admin interface,
	 * frontend functionality, and more.
	 *
	 * Files are conditionally loaded based on context (admin vs frontend)
	 * to improve performance by not loading unnecessary code.
	 *
	 * @since 1.0.0
	 */
	private function includes() {
		// Core includes - loaded on both admin and frontend.
		require_once BP_DEV_TOOLS_INCLUDES_DIR . 'class-database.php';
		require_once BP_DEV_TOOLS_INCLUDES_DIR . 'class-install.php';
		
		// Admin includes - only loaded in WordPress admin area.
		if ( is_admin() ) {
			require_once BP_DEV_TOOLS_INCLUDES_DIR . 'admin/class-admin.php';
			
			// Load tool classes.
			require_once BP_DEV_TOOLS_INCLUDES_DIR . 'tools/class-slug-scanner.php';
		}

		// Frontend includes - only loaded on public-facing pages.
		if ( ! is_admin() ) {
			require_once BP_DEV_TOOLS_INCLUDES_DIR . 'frontend/class-frontend.php';
		}
	}

	/**
	 * Hook into actions and filters.
	 *
	 * Sets up all WordPress hooks that the plugin needs to function.
	 * This includes activation, deactivation, and initialization hooks.
	 *
	 * WordPress uses hooks to allow plugins to interact with core functionality
	 * without modifying core files.
	 *
	 * @since 1.0.0
	 */
	private function init_hooks() {
		// Initialize plugin components after WordPress is fully loaded.
		add_action( 'plugins_loaded', array( $this, 'init' ), 10 );
		
		// Load plugin text domain for translations.
		add_action( 'init', array( $this, 'load_textdomain' ) );
		
		// Register activation and deactivation hooks.
		register_activation_hook( BP_DEV_TOOLS_PLUGIN_FILE, array( 'BP_Dev_Tools_Install', 'activate' ) );
		register_deactivation_hook( BP_DEV_TOOLS_PLUGIN_FILE, array( 'BP_Dev_Tools_Install', 'deactivate' ) );
	}

	/**
	 * Initialize plugin components.
	 *
	 * Creates instances of all the main plugin classes and sets up
	 * the database handler, admin interface, and frontend functionality.
	 *
	 * This method is called on the 'plugins_loaded' hook to ensure
	 * WordPress is fully initialized before we set up our components.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		// Initialize database handler.
		$this->database = new BP_Dev_Tools_Database();
		
		// Initialize admin interface if in admin area.
		if ( is_admin() ) {
			$this->admin = new BP_Dev_Tools_Admin();
			
			// Initialize tools.
			new BP_Dev_Tools_Slug_Scanner();
		}

		// Initialize frontend if not in admin area.
		if ( ! is_admin() ) {
			new BP_Dev_Tools_Frontend();
		}

		/**
		 * Fires after the plugin is fully initialized.
		 *
		 * This action hook allows other plugins or themes to hook into
		 * your plugin after it's fully loaded.
		 *
		 * @since 1.0.0
		 */
		do_action( 'bp_dev_tools_init' );
	}

	/**
	 * Load plugin text domain for translations.
	 *
	 * Makes the plugin translation-ready by loading the appropriate
	 * language files from the /languages directory.
	 *
	 * Translation files should be named: bp-dev-tools-{locale}.mo
	 * For example: bp-dev-tools-es_ES.mo for Spanish
	 *
	 * @since 1.0.0
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			'bp-dev-tools',
			false,
			dirname( BP_DEV_TOOLS_PLUGIN_BASENAME ) . '/languages'
		);
	}

	/**
	 * Get the plugin URL.
	 *
	 * Returns the plugin's base URL without a trailing slash.
	 * Useful for enqueueing assets and creating links.
	 *
	 * @since 1.0.0
	 * @return string Plugin URL.
	 */
	public function plugin_url() {
		return untrailingslashit( BP_DEV_TOOLS_PLUGIN_URL );
	}

	/**
	 * Get the plugin path.
	 *
	 * Returns the plugin's base filesystem path without a trailing slash.
	 * Useful for including files and accessing plugin directories.
	 *
	 * @since 1.0.0
	 * @return string Plugin path.
	 */
	public function plugin_path() {
		return untrailingslashit( BP_DEV_TOOLS_PLUGIN_DIR );
	}
}

/**
 * Returns the main instance of BP_Dev_Tools.
 *
 * This function is the main access point for the plugin.
 * Use this function like a global variable, except without needing
 * to declare the global.
 *
 * Example usage:
 * <?php BP_Dev_Tools()->database->method_name(); ?>
 * <?php $version = BP_Dev_Tools()::VERSION; ?>
 *
 * @since 1.0.0
 * @return BP_Dev_Tools The main plugin instance.
 */
function BP_Dev_Tools() {
	return BP_Dev_Tools::instance();
}

// Initialize the plugin.
BP_Dev_Tools();
