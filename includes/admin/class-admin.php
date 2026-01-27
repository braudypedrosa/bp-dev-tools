<?php
/**
 * Admin Interface Class
 *
 * Handles all WordPress admin area functionality for BP Dev Tools.
 * This class acts as a bridge between WordPress and the Vue.js frontend.
 *
 * @package BP_Dev_Tools
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class BP_Dev_Tools_Admin
 *
 * Manages the admin interface for the plugin.
 * The actual UI is built with Vue 3 + Tailwind CSS.
 * This class handles WordPress integration and provides data to the Vue app.
 *
 * @since 1.0.0
 */
class BP_Dev_Tools_Admin {

	/**
	 * Available tools registry
	 *
	 * @var array
	 */
	private $available_tools = array();

	/**
	 * Constructor.
	 *
	 * Sets up admin hooks and initializes the tool registry.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->register_available_tools();
		$this->init_hooks();
	}

	/**
	 * Register all available tools.
	 *
	 * This method defines all tools that can be enabled in the plugin.
	 * Each tool has a unique ID, title, description, and icon.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function register_available_tools() {
		/**
		 * Define available development tools.
		 *
		 * Each tool should have:
		 * - id: Unique identifier (slug format: lowercase, hyphens)
		 * - title: Display name
		 * - description: What the tool does
		 * - icon: Dashicon class name (dashicons-*)
		 * - enabled: Whether it's currently enabled
		 */
		$this->available_tools = array(
			'slug-scanner' => array(
				'id'          => 'slug-scanner',
				'title'       => __( 'Slug Scanner', 'bp-dev-tools' ),
				'description' => __( 'Check if URL slugs exist in your WordPress site. Paste a list of URLs to see which posts are found and which are missing, then create posts for missing slugs with one click.', 'bp-dev-tools' ),
				'icon'        => 'dashicons-search',
			),
			'bulk-create-post' => array(
				'id'          => 'bulk-create-post',
				'title'       => __( 'Bulk Create Post', 'bp-dev-tools' ),
				'description' => __( 'Create multiple posts or pages at once by pasting one title per line, then generate them in bulk.', 'bp-dev-tools' ),
				'icon'        => 'dashicons-media-text',
			),
		);

		/**
		 * Filter available dev tools.
		 *
		 * Allows other plugins or themes to add custom tools to the registry.
		 *
		 * @since 1.0.0
		 * @param array $tools Array of available tools.
		 */
		$this->available_tools = apply_filters( 'bp_dev_tools_available_tools', $this->available_tools );
	}

	/**
	 * Initialize admin hooks.
	 *
	 * Registers all WordPress admin hooks needed for the plugin.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function init_hooks() {
		// Show activation notice after plugin activation.
		add_action( 'admin_notices', array( $this, 'activation_notice' ) );

		// Enqueue admin styles and scripts.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );

		// Add settings page to WordPress admin menu.
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );

		// Register plugin settings.
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		// Add plugin action links (in plugins list).
		add_filter( 'plugin_action_links_' . BP_DEV_TOOLS_PLUGIN_BASENAME, array( $this, 'plugin_action_links' ) );
	}

	/**
	 * Show activation notice.
	 *
	 * Displays a welcome message after plugin activation.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function activation_notice() {
		// Check if activation transient exists.
		if ( ! get_transient( 'bp_dev_tools_activated' ) ) {
			return;
		}

		// Delete transient so notice only shows once.
		delete_transient( 'bp_dev_tools_activated' );
		?>
		<div class="notice notice-success is-dismissible">
			<p>
				<?php 
				printf(
					/* translators: %s: Plugin name */
					esc_html__( '%s has been activated successfully! Visit the settings page to enable your dev tools.', 'bp-dev-tools' ),
					'<strong>BP Dev Tools</strong>'
				);
				?>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=bp-dev-tools' ) ); ?>" class="button button-primary" style="margin-left: 10px;">
					<?php esc_html_e( 'Get Started', 'bp-dev-tools' ); ?>
				</a>
			</p>
		</div>
		<?php
	}

	/**
	 * Enqueue admin styles and scripts.
	 *
	 * Loads the compiled Vue app and Tailwind CSS.
	 *
	 * @since 1.0.0
	 * @param string $hook The current admin page hook.
	 * @return void
	 */
	public function enqueue_admin_assets( $hook ) {
		// Only load on our plugin's settings page.
		if ( 'toplevel_page_bp-dev-tools' !== $hook ) {
			return;
		}

		// Enqueue compiled admin CSS (includes Tailwind).
		wp_enqueue_style(
			'bp-dev-tools-admin',
			BP_DEV_TOOLS_ASSETS_URL . 'css/admin.css',
			array(),
			BP_DEV_TOOLS_VERSION
		);

		// Enqueue compiled Vue app.
		wp_enqueue_script(
			'bp-dev-tools-admin',
			BP_DEV_TOOLS_ASSETS_URL . 'js/admin.js',
			array(),
			BP_DEV_TOOLS_VERSION,
			true
		);

		// Get current settings and add enabled status to tools.
		$settings = get_option( 'bp_dev_tools_settings', array() );
		$enabled_tools = isset( $settings['enabled_tools'] ) ? $settings['enabled_tools'] : array();
		
		$tools_with_status = array();
		foreach ( $this->available_tools as $tool ) {
			$tool['enabled'] = in_array( $tool['id'], $enabled_tools, true );
			$tools_with_status[] = $tool;
		}

		// Pass data from PHP to Vue app.
		wp_localize_script(
			'bp-dev-tools-admin',
			'bpDevToolsAdmin',
			array(
				'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
				'nonce'     => wp_create_nonce( 'bp_dev_tools_admin_nonce' ),
				'pageUrl'   => admin_url( 'admin.php?page=bp-dev-tools' ),
				'adminUrl'  => admin_url(),
				'version'   => BP_DEV_TOOLS_VERSION,
				'strings'   => array(
					'saved'         => __( 'Settings saved successfully!', 'bp-dev-tools' ),
					'error'         => __( 'An error occurred. Please try again.', 'bp-dev-tools' ),
					'toolEnabled'   => __( 'Tool enabled successfully', 'bp-dev-tools' ),
					'toolDisabled'  => __( 'Tool disabled successfully', 'bp-dev-tools' ),
				),
			)
		);

		// Pass tools data to Vue app.
		wp_localize_script(
			'bp-dev-tools-admin',
			'bpDevToolsData',
			array(
				'tools' => $tools_with_status,
			)
		);
	}

	/**
	 * Add settings page to WordPress admin menu.
	 *
	 * Creates a top-level menu item for BP Dev Tools.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function add_settings_page() {
		add_menu_page(
			__( 'BP Dev Tools', 'bp-dev-tools' ),           // Page title
			__( 'Dev Tools', 'bp-dev-tools' ),              // Menu title
			'manage_options',                                // Capability
			'bp-dev-tools',                                  // Menu slug
			array( $this, 'render_settings_page' ),          // Callback
			'dashicons-admin-tools',                         // Icon (Dashicon)
			30                                               // Position
		);
	}

	/**
	 * Render settings page HTML.
	 *
	 * This simply outputs a mount point for the Vue app.
	 * All UI is handled by Vue 3.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function render_settings_page() {
		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<!-- Vue App Mount Point -->
			<div id="bp-dev-tools-app"></div>
		</div>
		<?php
	}

	/**
	 * Register plugin settings.
	 *
	 * Registers settings with WordPress Settings API and AJAX handlers.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function register_settings() {
		register_setting(
			'bp_dev_tools_settings_group',
			'bp_dev_tools_settings',
			array( $this, 'sanitize_settings' )
		);

		// Register AJAX handler for tool toggle.
		add_action( 'wp_ajax_bp_dev_tools_toggle_tool', array( $this, 'ajax_toggle_tool' ) );
		
		// Register AJAX handler for checking updates.
		add_action( 'wp_ajax_bp_dev_tools_check_updates', array( $this, 'ajax_check_updates' ) );
		
		// Register AJAX handler for getting update status.
		add_action( 'wp_ajax_bp_dev_tools_get_update_status', array( $this, 'ajax_get_update_status' ) );
	}

	/**
	 * AJAX handler for toggling tools.
	 *
	 * Handles enable/disable of individual tools via AJAX from Vue app.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function ajax_toggle_tool() {
		// Verify nonce.
		check_ajax_referer( 'bp_dev_tools_admin_nonce', 'nonce' );

		// Check capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'bp-dev-tools' ) ) );
		}

		// Get parameters.
		$tool_id = isset( $_POST['tool_id'] ) ? sanitize_key( $_POST['tool_id'] ) : '';
		$enabled = isset( $_POST['enabled'] ) && 'true' === $_POST['enabled'];

		if ( empty( $tool_id ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid tool ID.', 'bp-dev-tools' ) ) );
		}

		// Get current settings.
		$settings = get_option( 'bp_dev_tools_settings', array() );
		$enabled_tools = isset( $settings['enabled_tools'] ) ? $settings['enabled_tools'] : array();

		// Update enabled tools array.
		if ( $enabled ) {
			if ( ! in_array( $tool_id, $enabled_tools, true ) ) {
				$enabled_tools[] = $tool_id;
			}
		} else {
			$enabled_tools = array_diff( $enabled_tools, array( $tool_id ) );
		}

		// Save settings.
		$settings['enabled_tools'] = array_values( $enabled_tools );
		update_option( 'bp_dev_tools_settings', $settings );

		wp_send_json_success( array(
			'message' => $enabled ? __( 'Tool enabled successfully.', 'bp-dev-tools' ) : __( 'Tool disabled successfully.', 'bp-dev-tools' ),
			'enabled_count' => count( $enabled_tools ),
		) );
	}

	/**
	 * AJAX handler for checking plugin updates.
	 *
	 * Triggers WordPress to check for updates and returns the latest available version.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function ajax_check_updates() {
		// Verify nonce.
		check_ajax_referer( 'bp_dev_tools_admin_nonce', 'nonce' );

		// Check capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'bp-dev-tools' ) ) );
		}

		// Clear all update caches to force fresh check.
		delete_site_transient( 'update_plugins' );
		delete_option( 'puc_external_updates_bp-dev-tools' );
		
		// Clear any PUC request info cache.
		global $wpdb;
		$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '%puc_request_info_result%bp-dev-tools%'" );
		
		// Trigger immediate check.
		wp_update_plugins();
		
		// Give it a moment to fetch from GitHub.
		sleep( 2 );

		// Get update information.
		$update_plugins = get_site_transient( 'update_plugins' );
		$plugin_slug = 'bp-dev-tools/bp-dev-tools.php';
		
		$update_available = false;
		$latest_version = BP_DEV_TOOLS_VERSION;
		$release_url = null;

		if ( isset( $update_plugins->response[ $plugin_slug ] ) ) {
			$update_info = $update_plugins->response[ $plugin_slug ];
			$update_available = true;
			$latest_version = $update_info->new_version;
			$release_url = isset( $update_info->url ) ? $update_info->url : null;
		}

		wp_send_json_success( array(
			'update_available' => $update_available,
			'current_version' => BP_DEV_TOOLS_VERSION,
			'latest_version' => $latest_version,
			'release_url' => $release_url,
		) );
	}

	/**
	 * AJAX handler for getting current update status.
	 *
	 * Returns the current update status without forcing a new check.
	 * This is used on page load to show persistent update notifications.
	 *
	 * @since 1.0.3
	 * @return void
	 */
	public function ajax_get_update_status() {
		// Verify nonce.
		check_ajax_referer( 'bp_dev_tools_admin_nonce', 'nonce' );

		// Check capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'bp-dev-tools' ) ) );
		}

		// Get update information from transient.
		$update_plugins = get_site_transient( 'update_plugins' );
		$plugin_slug = 'bp-dev-tools/bp-dev-tools.php';
		
		$update_available = false;
		$latest_version = BP_DEV_TOOLS_VERSION;
		$release_url = null;

		if ( isset( $update_plugins->response[ $plugin_slug ] ) ) {
			$update_info = $update_plugins->response[ $plugin_slug ];
			$update_available = true;
			$latest_version = $update_info->new_version;
			$release_url = isset( $update_info->url ) ? $update_info->url : null;
		}

		wp_send_json_success( array(
			'update_available' => $update_available,
			'current_version' => BP_DEV_TOOLS_VERSION,
			'latest_version' => $latest_version,
			'release_url' => $release_url,
		) );
	}

	/**
	 * Sanitize settings before saving.
	 *
	 * @since 1.0.0
	 * @param array $input User-submitted settings data.
	 * @return array Sanitized settings data.
	 */
	public function sanitize_settings( $input ) {
		$sanitized = array();

		// Sanitize enabled tools array.
		if ( isset( $input['enabled_tools'] ) && is_array( $input['enabled_tools'] ) ) {
			$sanitized['enabled_tools'] = array_map( 'sanitize_key', $input['enabled_tools'] );
		} else {
			$sanitized['enabled_tools'] = array();
		}

		return $sanitized;
	}

	/**
	 * Add plugin action links.
	 *
	 * Adds "Settings" link on the plugins page.
	 *
	 * @since 1.0.0
	 * @param array $links Existing plugin action links.
	 * @return array Modified plugin action links.
	 */
	public function plugin_action_links( $links ) {
		$settings_link = sprintf(
			'<a href="%s">%s</a>',
			admin_url( 'admin.php?page=bp-dev-tools' ),
			__( 'Settings', 'bp-dev-tools' )
		);

		array_unshift( $links, $settings_link );

		return $links;
	}
}
