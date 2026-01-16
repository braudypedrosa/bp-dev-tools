<?php
/**
 * Frontend Interface Class
 *
 * Handles all frontend functionality for the plugin.
 * This includes displaying content, handling user interactions, and AJAX requests.
 *
 * @package BP_Dev_Tools
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class BP_Plugin_Boilerplate_Frontend
 *
 * Manages the frontend display and functionality of the plugin.
 * This class handles public-facing features, asset enqueuing,
 * shortcodes, and AJAX handlers.
 *
 * BEGINNER'S NOTE:
 * - Frontend = what visitors see on your website (not the admin area)
 * - This class only loads when NOT in admin area (for better performance)
 * - Handle all public-facing features here
 * - Always enqueue scripts/styles properly (don't hardcode <script> tags)
 *
 * @since 1.0.0
 */
class BP_Dev_Tools_Frontend {

	/**
	 * Constructor.
	 *
	 * Sets up frontend hooks and initializes frontend components.
	 * This is called when the class is instantiated.
	 *
	 * BEGINNER'S NOTE:
	 * - __construct() runs automatically when you create a new instance
	 * - It's used to set up hooks and initial configuration
	 * - Keep it lightweight - heavy operations should be in separate methods
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->init_hooks();
	}

	/**
	 * Initialize frontend hooks.
	 *
	 * Registers all WordPress frontend hooks needed for the plugin
	 * to function on the public-facing side.
	 *
	 * BEGINNER'S NOTE:
	 * - Hooks let you tap into WordPress at specific points
	 * - add_action() runs your code when WordPress does something
	 * - add_filter() lets you modify data as it passes through WordPress
	 * - add_shortcode() creates custom [shortcode] tags
	 *
	 * Common frontend hooks:
	 * - wp_enqueue_scripts: Load CSS/JS files
	 * - wp_head: Add content to <head> section
	 * - wp_footer: Add content before </body>
	 * - the_content: Modify post/page content
	 * - template_redirect: Redirect users or change templates
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function init_hooks() {
		// Enqueue frontend scripts and styles.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );
		
		// Register shortcodes.
		// Uncomment and modify when you want to add shortcodes.
		// add_shortcode( 'bp_plugin_example', array( $this, 'render_shortcode' ) );
		
		// Register AJAX handlers for logged-in users.
		// Uncomment when you need AJAX functionality.
		// add_action( 'wp_ajax_bp_plugin_action', array( $this, 'ajax_handler' ) );
		
		// Register AJAX handlers for non-logged-in users (guests).
		// add_action( 'wp_ajax_nopriv_bp_plugin_action', array( $this, 'ajax_handler' ) );
		
		// Modify post content (example).
		// add_filter( 'the_content', array( $this, 'modify_content' ) );
		
		// Add custom body classes.
		// add_filter( 'body_class', array( $this, 'add_body_classes' ) );
	}

	/**
	 * Enqueue frontend assets.
	 *
	 * Loads CSS and JavaScript files needed for the frontend.
	 * Only loads on pages where the plugin features are needed.
	 *
	 * BEGINNER'S NOTE:
	 * - wp_enqueue_style() loads CSS files the WordPress way
	 * - wp_enqueue_script() loads JavaScript files the WordPress way
	 * - Never hardcode <link> or <script> tags in templates
	 * - WordPress handles loading order and prevents duplicate loading
	 * - Always use version numbers for cache busting
	 *
	 * Best practices:
	 * - Only load assets on pages that need them
	 * - Specify dependencies (e.g., jQuery) to ensure correct load order
	 * - Use minified versions in production for better performance
	 * - Load scripts in footer (last parameter = true) when possible
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function enqueue_frontend_assets() {
		// Only load on specific pages/post types if needed.
		// Example: Only load on single posts and pages.
		// if ( ! is_singular( array( 'post', 'page' ) ) ) {
		//     return;
		// }

		// Check if plugin is enabled.
		$settings = get_option( 'bp_dev_tools_settings', array() );
		if ( empty( $settings['enabled'] ) ) {
			return;
		}

		// Enqueue frontend CSS.
		// Parameters: handle (unique ID), file path, dependencies, version, media type
		wp_enqueue_style(
			'bp-dev-tools-frontend',
			BP_DEV_TOOLS_ASSETS_URL . 'css/frontend.css',
			array(), // Dependencies (other stylesheets this needs)
			BP_DEV_TOOLS_VERSION,
			'all' // Media type (all, screen, print, etc.)
		);

		// Enqueue frontend JS.
		// Parameters: handle, file path, dependencies, version, in_footer
		wp_enqueue_script(
			'bp-dev-tools-frontend',
			BP_DEV_TOOLS_ASSETS_URL . 'js/frontend.js',
			array( 'jquery' ), // Requires jQuery
			BP_DEV_TOOLS_VERSION,
			true // Load in footer (better for page speed)
		);

		// Pass data from PHP to JavaScript.
		// Makes PHP variables accessible in your JS as an object.
		wp_localize_script(
			'bp-dev-tools-frontend',
			'bpDevTools', // JavaScript object name (use in JS as: bpDevTools.ajaxUrl)
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'bp_dev_tools_nonce' ),
				'postId'  => get_the_ID(),
				'strings' => array(
					'loading'      => __( 'Loading...', 'bp-dev-tools' ),
					'error'        => __( 'An error occurred. Please try again.', 'bp-dev-tools' ),
					'success'      => __( 'Success!', 'bp-dev-tools' ),
					'confirm'      => __( 'Are you sure?', 'bp-dev-tools' ),
				),
			)
		);
	}

	/**
	 * Render shortcode output.
	 *
	 * Processes and displays content when [bp_plugin_example] shortcode is used.
	 *
	 * BEGINNER'S NOTE:
	 * - Shortcodes are custom tags users can add to posts/pages
	 * - Example: [bp_plugin_example id="123" color="blue"]
	 * - $atts contains the shortcode attributes as an array
	 * - Always use shortcode_atts() to set defaults and sanitize
	 * - Always return content, never echo it (important!)
	 * - Use output buffering (ob_start/ob_get_clean) for complex HTML
	 *
	 * Usage examples:
	 * [bp_plugin_example] - Uses default attributes
	 * [bp_plugin_example id="5"] - Custom ID
	 * [bp_plugin_example id="5" title="My Title"] - Multiple attributes
	 *
	 * @since 1.0.0
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content Content between opening and closing shortcode tags.
	 * @return string Shortcode output HTML.
	 */
	public function render_shortcode( $atts, $content = null ) {
		// Parse shortcode attributes with defaults.
		$atts = shortcode_atts(
			array(
				'id'    => 0,
				'title' => __( 'Default Title', 'bp-plugin-boilerplate' ),
				'type'  => 'default',
			),
			$atts,
			'bp_plugin_example' // Shortcode tag name
		);

		// Sanitize attributes.
		$id    = absint( $atts['id'] );
		$title = sanitize_text_field( $atts['title'] );
		$type  = sanitize_key( $atts['type'] );

		// Start output buffering to capture HTML.
		ob_start();
		?>
		<div class="bp-dev-tools-shortcode" data-id="<?php echo esc_attr( $id ); ?>">
			<h3><?php echo esc_html( $title ); ?></h3>
			
			<?php if ( $content ) : ?>
				<div class="content">
					<?php echo wp_kses_post( $content ); ?>
				</div>
			<?php endif; ?>
			
			<p><?php esc_html_e( 'This is example shortcode output.', 'bp-dev-tools' ); ?></p>
		</div>
		<?php
		// Return the buffered content.
		return ob_get_clean();
	}

	/**
	 * AJAX handler example.
	 *
	 * Processes AJAX requests from the frontend.
	 *
	 * BEGINNER'S NOTE:
	 * - AJAX = Asynchronous JavaScript And XML (updates page without reload)
	 * - check_ajax_referer() verifies the request is legitimate (security)
	 * - $_POST contains data sent from JavaScript
	 * - wp_send_json_success() sends success response and stops execution
	 * - wp_send_json_error() sends error response and stops execution
	 * - Always sanitize input data before using it
	 *
	 * JavaScript side (in frontend.js):
	 * $.ajax({
	 *     url: bpPluginBoilerplate.ajaxUrl,
	 *     type: 'POST',
	 *     data: {
	 *         action: 'bp_plugin_action',
	 *         nonce: bpPluginBoilerplate.nonce,
	 *         item_id: 123
	 *     },
	 *     success: function(response) {
	 *         console.log(response.data);
	 *     }
	 * });
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function ajax_handler() {
		// Verify nonce for security.
		check_ajax_referer( 'bp_dev_tools_nonce', 'nonce' );

		// Get and sanitize input data.
		$item_id = isset( $_POST['item_id'] ) ? absint( $_POST['item_id'] ) : 0;

		// Validate input.
		if ( ! $item_id ) {
			wp_send_json_error( array(
				'message' => __( 'Invalid item ID.', 'bp-dev-tools' ),
			) );
		}

		// Perform your logic here.
		// Example: Get data from database, process something, etc.
		
		// Send success response.
		wp_send_json_success( array(
			'message' => __( 'Action completed successfully!', 'bp-dev-tools' ),
			'item_id' => $item_id,
			// Include any data you want to send back to JavaScript
		) );
	}

	/**
	 * Modify post content.
	 *
	 * Adds custom content before or after post content.
	 *
	 * BEGINNER'S NOTE:
	 * - the_content filter runs every time post/page content is displayed
	 * - You can add content before, after, or replace the content
	 * - Always check conditions (post type, category, etc.) before modifying
	 * - Return the modified content, don't echo it
	 * - is_main_query() ensures you only modify main post content, not sidebar widgets
	 *
	 * Common uses:
	 * - Add social sharing buttons after content
	 * - Add related posts at the end
	 * - Add notices or warnings before content
	 * - Insert ads or custom CTAs
	 *
	 * @since 1.0.0
	 * @param string $content The post content.
	 * @return string Modified post content.
	 */
	public function modify_content( $content ) {
		// Only modify on singular posts/pages in the main query.
		if ( ! is_singular() || ! is_main_query() ) {
			return $content;
		}

		// Check if plugin is enabled for this post type.
		$settings = get_option( 'bp_dev_tools_settings', array() );
		$enabled_post_types = isset( $settings['enabled_post_types'] ) ? $settings['enabled_post_types'] : array( 'post' );
		
		if ( ! in_array( get_post_type(), $enabled_post_types, true ) ) {
			return $content;
		}

		// Add content before the main content.
		$before = '<div class="bp-dev-tools-before-content">';
		$before .= '<p>' . esc_html__( 'Custom content before post.', 'bp-dev-tools' ) . '</p>';
		$before .= '</div>';

		// Add content after the main content.
		$after = '<div class="bp-dev-tools-after-content">';
		$after .= '<p>' . esc_html__( 'Custom content after post.', 'bp-dev-tools' ) . '</p>';
		$after .= '</div>';

		// Return modified content.
		return $before . $content . $after;
	}

	/**
	 * Add custom body classes.
	 *
	 * Adds CSS classes to the <body> tag based on plugin conditions.
	 *
	 * BEGINNER'S NOTE:
	 * - body_class filter lets you add CSS classes to <body> tag
	 * - Useful for conditional styling based on plugin state
	 * - Classes are added to the existing array
	 * - Use for enabling/disabling CSS features
	 * - Keep class names unique to avoid conflicts
	 *
	 * Example uses:
	 * - .bp-plugin-active { } (style when plugin features are active)
	 * - .bp-plugin-dark-mode { } (style for dark mode)
	 * - .bp-plugin-logged-in { } (different styles for logged-in users)
	 *
	 * @since 1.0.0
	 * @param array $classes Existing body classes.
	 * @return array Modified body classes.
	 */
	public function add_body_classes( $classes ) {
		$settings = get_option( 'bp_dev_tools_settings', array() );

		// Add class if plugin is enabled.
		if ( ! empty( $settings['enabled'] ) ) {
			$classes[] = 'bp-dev-tools-active';
		}

		// Add class if on enabled post type.
		if ( is_singular() ) {
			$enabled_post_types = isset( $settings['enabled_post_types'] ) ? $settings['enabled_post_types'] : array();
			
			if ( in_array( get_post_type(), $enabled_post_types, true ) ) {
				$classes[] = 'bp-dev-tools-enabled';
			}
		}

		// Add conditional classes.
		if ( is_user_logged_in() ) {
			$classes[] = 'bp-dev-tools-logged-in';
		}

		return $classes;
	}

	/**
	 * Example: Get data for display.
	 *
	 * Retrieves data from database or other sources for frontend display.
	 *
	 * BEGINNER'S NOTE:
	 * - Create helper methods like this for reusable functionality
	 * - Use WordPress caching (transients) for expensive operations
	 * - Always validate and sanitize data
	 * - Return data in a consistent format (array, object, etc.)
	 *
	 * @since 1.0.0
	 * @param int $post_id Post ID to get data for.
	 * @return array|false Data array or false on failure.
	 */
	public function get_display_data( $post_id ) {
		$post_id = absint( $post_id );

		if ( ! $post_id ) {
			return false;
		}

		// Try to get cached data first (improves performance).
		$cache_key = 'bp_dev_tools_data_' . $post_id;
		$cached_data = get_transient( $cache_key );

		if ( false !== $cached_data ) {
			return $cached_data;
		}

		// Get fresh data if cache doesn't exist.
		$data = array(
			'post_id'    => $post_id,
			'title'      => get_the_title( $post_id ),
			'permalink'  => get_permalink( $post_id ),
			'meta_value' => get_post_meta( $post_id, '_bp_dev_tools_meta', true ),
		);

		// Cache the data for 1 hour.
		set_transient( $cache_key, $data, HOUR_IN_SECONDS );

		return $data;
	}
}
