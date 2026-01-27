<?php
/**
 * Bulk Create Posts Tool
 *
 * Creates multiple posts from a list of titles.
 *
 * @package BP_Dev_Tools
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class BP_Dev_Tools_Bulk_Create_Posts
 *
 * Handles bulk post creation functionality.
 *
 * @since 1.0.0
 */
class BP_Dev_Tools_Bulk_Create_Posts {

	/**
	 * Maximum number of titles allowed per request.
	 *
	 * @since 1.0.0
	 * @var int
	 */
	const MAX_TITLES = 20;

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
		add_action( 'wp_ajax_bp_dev_tools_bulk_create_posts', array( $this, 'ajax_bulk_create_posts' ) );
	}

	/**
	 * AJAX handler for bulk creating posts.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function ajax_bulk_create_posts() {
		// Verify nonce.
		check_ajax_referer( 'bp_dev_tools_admin_nonce', 'nonce' );

		$titles_input = isset( $_POST['titles'] ) ? sanitize_textarea_field( $_POST['titles'] ) : '';
		$post_type    = isset( $_POST['post_type'] ) ? sanitize_key( $_POST['post_type'] ) : 'page';
		$post_status  = isset( $_POST['post_status'] ) ? sanitize_key( $_POST['post_status'] ) : 'draft';
		$skip_empty   = isset( $_POST['skip_empty'] ) ? ( 'true' === $_POST['skip_empty'] ) : true;
		$allow_duplicates = isset( $_POST['allow_duplicates'] ) ? ( 'true' === $_POST['allow_duplicates'] ) : false;

		if ( empty( $titles_input ) ) {
			wp_send_json_error( array( 'message' => __( 'No titles provided.', 'bp-dev-tools' ) ) );
		}

		$post_type_object = $this->get_valid_post_type( $post_type );
		if ( ! $post_type_object ) {
			wp_send_json_error( array( 'message' => __( 'Invalid post type.', 'bp-dev-tools' ) ) );
		}

		$post_status = $this->get_valid_post_status( $post_status );

		if ( 'publish' === $post_status && ! current_user_can( $post_type_object->cap->publish_posts ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'bp-dev-tools' ) ) );
		}

		if ( 'publish' !== $post_status && ! current_user_can( $post_type_object->cap->create_posts ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'bp-dev-tools' ) ) );
		}

		$lines = array_map( 'trim', explode( "\n", $titles_input ) );
		$lines = array_values( $lines );

		$non_empty_lines = array_values( array_filter( $lines, 'strlen' ) );

		if ( $skip_empty ) {
			$lines = $non_empty_lines;
		}

		if ( empty( $lines ) && empty( $non_empty_lines ) ) {
			wp_send_json_error( array( 'message' => __( 'No valid titles found.', 'bp-dev-tools' ) ) );
		}

		if ( count( $non_empty_lines ) > self::MAX_TITLES ) {
			wp_send_json_error(
				array(
					'message' => sprintf(
						/* translators: %d: Maximum number of titles allowed. */
						esc_html__( 'Please limit your list to %d titles per request.', 'bp-dev-tools' ),
						self::MAX_TITLES
					),
				)
			);
		}

		$created = array();
		$failed  = array();
		$seen    = array();

		foreach ( $lines as $line ) {
			$title = sanitize_text_field( $line );

			if ( '' === $title ) {
				$failed[] = array(
					'title'   => $line,
					'message' => __( 'Empty title skipped.', 'bp-dev-tools' ),
				);
				continue;
			}

			if ( ! $allow_duplicates ) {
				if ( isset( $seen[ $title ] ) ) {
					$failed[] = array(
						'title'   => $title,
						'message' => __( 'Duplicate title in list.', 'bp-dev-tools' ),
					);
					continue;
				}

				$seen[ $title ] = true;

				$existing = get_page_by_title( $title, OBJECT, $post_type );
				if ( $existing && $existing->post_title === $title ) {
					$failed[] = array(
						'title'   => $title,
						'message' => __( 'A post with this title already exists.', 'bp-dev-tools' ),
					);
					continue;
				}
			}

			$post_name = '';
			if ( $allow_duplicates ) {
				$base_slug = sanitize_title( $title );
				$post_name = wp_unique_post_slug( $base_slug, 0, $post_status, $post_type, 0 );
			}

			$post_data = array(
				'post_title'  => $title,
				'post_type'   => $post_type,
				'post_status' => $post_status,
			);

			if ( $allow_duplicates && ! empty( $post_name ) ) {
				$post_data['post_name'] = $post_name;
			}

			$post_id = wp_insert_post( $post_data, true );

			if ( is_wp_error( $post_id ) ) {
				$failed[] = array(
					'title'   => $title,
					'message' => $post_id->get_error_message(),
				);
				continue;
			}

			$created[] = array(
				'title'     => $title,
				'post_id'   => $post_id,
				'type'      => $post_type,
				'status'    => get_post_status( $post_id ),
				'edit_url'  => get_edit_post_link( $post_id, 'raw' ),
				'view_url'  => get_permalink( $post_id ),
			);
		}

		wp_send_json_success(
			array(
				'created' => $created,
				'failed'  => $failed,
				'total'   => count( $lines ),
			)
		);
	}

	/**
	 * Validate a post type and ensure it is public.
	 *
	 * @since 1.0.0
	 * @param string $post_type Post type slug.
	 * @return WP_Post_Type|null Post type object or null if invalid.
	 */
	private function get_valid_post_type( $post_type ) {
		if ( empty( $post_type ) || ! post_type_exists( $post_type ) ) {
			return null;
		}

		$post_type_object = get_post_type_object( $post_type );
		if ( ! $post_type_object || ! $post_type_object->public ) {
			return null;
		}

		return $post_type_object;
	}

	/**
	 * Validate and normalize a post status.
	 *
	 * @since 1.0.0
	 * @param string $post_status Post status from user input.
	 * @return string Normalized post status.
	 */
	private function get_valid_post_status( $post_status ) {
		$allowed_statuses = array( 'draft', 'publish' );

		if ( in_array( $post_status, $allowed_statuses, true ) ) {
			return $post_status;
		}

		return 'draft';
	}
}
