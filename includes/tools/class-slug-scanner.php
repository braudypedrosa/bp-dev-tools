<?php
/**
 * Slug Scanner Tool
 *
 * Scans URLs to check if their slugs exist in WordPress.
 *
 * @package BP_Dev_Tools
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class BP_Dev_Tools_Slug_Scanner
 *
 * Handles slug scanning functionality.
 *
 * @since 1.0.0
 */
class BP_Dev_Tools_Slug_Scanner {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->init_hooks();
	}

	/**
	 * Initialize hooks.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function init_hooks() {
		// Get available post types.
		add_action( 'wp_ajax_bp_dev_tools_get_post_types', array( $this, 'ajax_get_post_types' ) );
		
		// Scan slugs.
		add_action( 'wp_ajax_bp_dev_tools_scan_slugs', array( $this, 'ajax_scan_slugs' ) );
		
		// Create post from slug.
		add_action( 'wp_ajax_bp_dev_tools_create_post', array( $this, 'ajax_create_post' ) );
	}

	/**
	 * Get available post types.
	 *
	 * Returns all public post types available in WordPress.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function ajax_get_post_types() {
		// Verify nonce.
		check_ajax_referer( 'bp_dev_tools_admin_nonce', 'nonce' );

		// Check capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'bp-dev-tools' ) ) );
		}

		// Get all public post types.
		$post_types = get_post_types(
			array(
				'public' => true,
			),
			'objects'
		);

		$formatted_types = array();
		foreach ( $post_types as $post_type ) {
			$formatted_types[] = array(
				'value' => $post_type->name,
				'label' => $post_type->labels->name,
			);
		}

		wp_send_json_success( array( 'post_types' => $formatted_types ) );
	}

	/**
	 * Scan slugs to check if they exist.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function ajax_scan_slugs() {
		// Verify nonce.
		check_ajax_referer( 'bp_dev_tools_admin_nonce', 'nonce' );

		// Check capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'bp-dev-tools' ) ) );
		}

		// Get parameters.
		$urls      = isset( $_POST['urls'] ) ? sanitize_textarea_field( $_POST['urls'] ) : '';
		$post_type = isset( $_POST['post_type'] ) ? sanitize_text_field( $_POST['post_type'] ) : '';

		if ( empty( $urls ) ) {
			wp_send_json_error( array( 'message' => __( 'No URLs provided.', 'bp-dev-tools' ) ) );
		}

		// Parse URLs into array.
		$urls_array = array_filter( array_map( 'trim', explode( "\n", $urls ) ) );

		$found     = array();
		$not_found = array();

		foreach ( $urls_array as $url ) {
			$slug = $this->extract_slug( $url );
			
			if ( empty( $slug ) ) {
				continue;
			}

			// Check if slug exists.
			$post = $this->find_post_by_slug( $slug, $post_type );

			if ( $post ) {
				$found[] = array(
					'url'       => $url,
					'slug'      => $slug,
					'post_id'   => $post->ID,
					'title'     => $post->post_title,
					'type'      => $post->post_type,
					'status'    => $post->post_status,
					'edit_url'  => get_edit_post_link( $post->ID, 'raw' ),
					'view_url'  => get_permalink( $post->ID ),
				);
			} else {
				$not_found[] = array(
					'url'  => $url,
					'slug' => $slug,
				);
			}
		}

		wp_send_json_success(
			array(
				'found'     => $found,
				'not_found' => $not_found,
				'total'     => count( $urls_array ),
			)
		);
	}

	/**
	 * Create a new post from a slug.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function ajax_create_post() {
		// Verify nonce.
		check_ajax_referer( 'bp_dev_tools_admin_nonce', 'nonce' );

		// Check capabilities.
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'bp-dev-tools' ) ) );
		}

		// Get parameters.
		$slug      = isset( $_POST['slug'] ) ? sanitize_title( $_POST['slug'] ) : '';
		$post_type = isset( $_POST['post_type'] ) ? sanitize_text_field( $_POST['post_type'] ) : 'post';

		if ( empty( $slug ) ) {
			wp_send_json_error( array( 'message' => __( 'No slug provided.', 'bp-dev-tools' ) ) );
		}

		// Create title from slug.
		$title = ucwords( str_replace( array( '-', '_' ), ' ', $slug ) );

		// Create the post.
		$post_id = wp_insert_post(
			array(
				'post_title'  => $title,
				'post_name'   => $slug,
				'post_type'   => $post_type,
				'post_status' => 'draft',
			)
		);

		if ( is_wp_error( $post_id ) ) {
			wp_send_json_error( array( 'message' => $post_id->get_error_message() ) );
		}

		wp_send_json_success(
			array(
				'post_id'  => $post_id,
				'edit_url' => get_edit_post_link( $post_id, 'raw' ),
				'message'  => __( 'Post created successfully!', 'bp-dev-tools' ),
			)
		);
	}

	/**
	 * Extract slug from URL.
	 *
	 * Removes domain and query parameters, keeping only the path/slug.
	 *
	 * @since 1.0.0
	 * @param string $url The URL to parse.
	 * @return string The extracted slug.
	 */
	private function extract_slug( $url ) {
		// Parse URL.
		$parsed = wp_parse_url( $url );
		
		if ( ! isset( $parsed['path'] ) ) {
			return '';
		}

		// Get path and remove leading/trailing slashes.
		$path = trim( $parsed['path'], '/' );
		
		// Return the last segment (the slug).
		$segments = explode( '/', $path );
		return end( $segments );
	}

	/**
	 * Find post by slug.
	 *
	 * @since 1.0.0
	 * @param string $slug      The slug to search for.
	 * @param string $post_type Optional. Post type to filter by.
	 * @return WP_Post|null The post object if found, null otherwise.
	 */
	private function find_post_by_slug( $slug, $post_type = '' ) {
		$args = array(
			'name'           => $slug,
			'posts_per_page' => 1,
			'post_status'    => 'any',
		);

		// Filter by post type if specified.
		if ( ! empty( $post_type ) ) {
			$args['post_type'] = $post_type;
		} else {
			$args['post_type'] = 'any';
		}

		$posts = get_posts( $args );

		return ! empty( $posts ) ? $posts[0] : null;
	}
}
