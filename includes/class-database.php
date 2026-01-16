<?php
/**
 * Database Handler Class
 *
 * Manages all database operations for the plugin.
 * This includes table creation, queries, and database maintenance.
 *
 * @package BP_Dev_Tools
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class BP_Dev_Tools_Database
 *
 * Handles all database-related operations for the plugin.
 * This class manages custom database tables (if needed) and provides
 * methods for CRUD (Create, Read, Update, Delete) operations.
 *
 * BEGINNER'S NOTE:
 * - CRUD stands for Create, Read, Update, Delete - the basic database operations
 * - This class centralizes all database logic in one place for easier maintenance
 * - Custom tables are only needed if WordPress's default tables aren't sufficient
 *
 * @since 1.0.0
 */
class BP_Dev_Tools_Database {

	/**
	 * The name of the main custom table.
	 *
	 * Example: If your plugin stores custom data, you might name this something like
	 * 'items_table' or 'records_table' based on what your plugin does.
	 *
	 * @var string
	 */
	public $main_table;

	/**
	 * Database version.
	 *
	 * Used for tracking database schema changes and migrations.
	 * Increment this when you modify the database structure so the plugin
	 * knows to run database updates.
	 *
	 * @var string
	 */
	private $db_version = '1.0.0';

	/**
	 * Constructor.
	 *
	 * Initializes the database handler by setting up table names
	 * with the WordPress database prefix. This ensures your tables
	 * don't conflict with other plugins or WordPress core tables.
	 *
	 * BEGINNER'S NOTE:
	 * - $wpdb is WordPress's database class that provides secure database access
	 * - $wpdb->prefix adds the WordPress table prefix (default: 'wp_') to your table name
	 * - This prevents table name conflicts between WordPress installations
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		global $wpdb;

		// Set table names with WordPress prefix.
		// IMPORTANT: Change 'bp_dev_tools_main' to match your plugin's needs.
		$this->main_table = $wpdb->prefix . 'bp_dev_tools_main';

		// Register tables with wpdb for easier access throughout WordPress.
		// This allows you to use $wpdb->bp_dev_tools_main instead of the full table name.
		$wpdb->bp_dev_tools_main = $this->main_table;
	}

	/**
	 * Create database tables.
	 *
	 * Creates all custom tables needed for the plugin.
	 * Uses dbDelta for safe table creation/updates.
	 * This method is called during plugin activation.
	 *
	 * BEGINNER'S NOTE:
	 * - dbDelta is a WordPress function that safely creates or updates tables
	 * - It compares the desired structure with existing tables and makes only necessary changes
	 * - The SQL syntax must be very specific for dbDelta to work correctly
	 * - Each field must be on its own line
	 * - There must be two spaces between PRIMARY KEY and the field name
	 * - You must use KEY instead of INDEX
	 *
	 * IMPORTANT: This code is commented out as a template.
	 * Uncomment and modify it when you need custom database tables.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function create_tables() {
		global $wpdb;

		// Include upgrade.php for dbDelta function.
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		// Get the correct character set and collation for the database.
		// This ensures your tables use the same encoding as WordPress.
		$charset_collate = $wpdb->get_charset_collate();

		/*
		 * EXAMPLE TABLE STRUCTURE
		 * 
		 * Uncomment and modify this code when you need a custom table.
		 * 
		 * IMPORTANT SQL FORMATTING RULES FOR dbDelta:
		 * 1. Each field must be on its own line
		 * 2. Use TWO SPACES between PRIMARY KEY and the field name
		 * 3. Use KEY not INDEX
		 * 4. Table and field names should be lowercase with underscores
		 * 
		 * Common Field Types:
		 * - bigint(20): Large integers (IDs, user IDs, post IDs)
		 * - varchar(255): Short text strings (names, emails)
		 * - longtext: Large text content (descriptions, content)
		 * - datetime: Date and time values
		 * - tinyint(1): Boolean values (0 or 1)
		 * - int(11): Regular integers (counts, scores)
		 */

		// Main table example.
		/*
		$main_table_sql = "CREATE TABLE {$this->main_table} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			post_id bigint(20) unsigned DEFAULT 0 COMMENT 'Related WordPress post ID',
			user_id bigint(20) unsigned DEFAULT 0 COMMENT 'Related WordPress user ID',
			title varchar(255) NOT NULL COMMENT 'Item title or name',
			description longtext COMMENT 'Item description or content',
			status varchar(20) DEFAULT 'active' COMMENT 'Status: active, inactive, pending',
			meta_value longtext COMMENT 'Additional metadata in JSON format',
			created_at datetime NOT NULL COMMENT 'Creation timestamp',
			updated_at datetime DEFAULT NULL COMMENT 'Last update timestamp',
			PRIMARY KEY  (id),
			KEY post_id (post_id),
			KEY user_id (user_id),
			KEY status (status),
			KEY created_at (created_at)
		) $charset_collate;";

		// Execute table creation query.
		// dbDelta will create the table if it doesn't exist,
		// or update it if the structure has changed.
		dbDelta( $main_table_sql );
		*/

		// Save database version to track schema changes.
		update_option( 'bp_dev_tools_db_version', $this->db_version );
	}

	/**
	 * Drop database tables.
	 *
	 * Removes all custom tables created by the plugin.
	 * This is typically called during plugin uninstallation.
	 * 
	 * USE WITH CAUTION - this will permanently delete all plugin data!
	 *
	 * BEGINNER'S NOTE:
	 * - DROP TABLE permanently deletes a database table and all its data
	 * - IF EXISTS prevents errors if the table doesn't exist
	 * - This should only be called during uninstallation, never during deactivation
	 * - Always give users the option to keep their data on uninstall
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function drop_tables() {
		global $wpdb;

		// Drop main table if it exists.
		// Uncomment this when you have custom tables to remove.
		// $wpdb->query( "DROP TABLE IF EXISTS {$this->main_table}" );

		// Delete database version option.
		delete_option( 'bp_dev_tools_db_version' );
	}

	/**
	 * Check if tables exist.
	 *
	 * Verifies that all required custom tables are present in the database.
	 * Useful for troubleshooting and health checks.
	 *
	 * BEGINNER'S NOTE:
	 * - SHOW TABLES LIKE queries the database for tables matching a pattern
	 * - Returns the table name if it exists, or NULL if it doesn't
	 * - Comparing with === ensures exact match (not just similar)
	 *
	 * @since 1.0.0
	 * @return bool True if all tables exist, false otherwise.
	 */
	public function tables_exist() {
		global $wpdb;

		// Check if main table exists.
		// Uncomment and modify when you have custom tables.
		// $main_exists = $wpdb->get_var( "SHOW TABLES LIKE '{$this->main_table}'" ) === $this->main_table;
		
		// Return true if all required tables exist.
		// return $main_exists;

		// For now, return true since we don't have custom tables yet.
		return true;
	}

	/**
	 * Get database version.
	 *
	 * Retrieves the current database schema version from options.
	 * This helps determine if database updates are needed.
	 *
	 * @since 1.0.0
	 * @return string|false Database version or false if not set.
	 */
	public function get_db_version() {
		return get_option( 'bp_dev_tools_db_version', false );
	}

	/**
	 * Check if database needs update.
	 *
	 * Compares the installed database version with the current version
	 * to determine if a schema update is needed.
	 *
	 * BEGINNER'S NOTE:
	 * - version_compare() is a PHP function that compares version numbers
	 * - The '<' parameter checks if the first version is less than the second
	 * - This ensures database updates run when the schema version increases
	 *
	 * @since 1.0.0
	 * @return bool True if update needed, false otherwise.
	 */
	public function needs_update() {
		$installed_version = $this->get_db_version();
		
		if ( ! $installed_version ) {
			return true;
		}

		return version_compare( $installed_version, $this->db_version, '<' );
	}

	/**
	 * Example CRUD Methods
	 *
	 * Below are template methods for common database operations.
	 * Uncomment and modify these when you need to interact with your custom tables.
	 */

	/**
	 * Insert a new record.
	 *
	 * Adds a new row to the database table with the provided data.
	 *
	 * BEGINNER'S NOTE:
	 * - $wpdb->insert() is a secure way to insert data into WordPress database
	 * - It automatically escapes data to prevent SQL injection attacks
	 * - The first parameter is the table name
	 * - The second parameter is an array of column => value pairs
	 * - The third parameter specifies data types for each value (%s = string, %d = integer, %f = float)
	 *
	 * @since 1.0.0
	 * @param array $data Array of column => value pairs.
	 * @return int|false The inserted row ID on success, false on failure.
	 */
	/*
	public function insert( $data ) {
		global $wpdb;

		$data['created_at'] = current_time( 'mysql' );

		$result = $wpdb->insert(
			$this->main_table,
			$data,
			array(
				'%d', // post_id
				'%d', // user_id
				'%s', // title
				'%s', // description
				'%s', // status
				'%s', // meta_value
				'%s', // created_at
			)
		);

		if ( $result ) {
			return $wpdb->insert_id;
		}

		return false;
	}
	*/

	/**
	 * Update an existing record.
	 *
	 * Updates a row in the database with new data.
	 *
	 * BEGINNER'S NOTE:
	 * - $wpdb->update() securely updates database rows
	 * - First parameter: table name
	 * - Second parameter: data to update (column => value)
	 * - Third parameter: WHERE clause (which rows to update)
	 * - Fourth parameter: data type format for update data
	 * - Fifth parameter: data type format for WHERE clause
	 *
	 * @since 1.0.0
	 * @param int   $id   Record ID to update.
	 * @param array $data Array of column => value pairs to update.
	 * @return bool True on success, false on failure.
	 */
	/*
	public function update( $id, $data ) {
		global $wpdb;

		$data['updated_at'] = current_time( 'mysql' );

		$result = $wpdb->update(
			$this->main_table,
			$data,
			array( 'id' => $id ),
			array(
				'%s', // title
				'%s', // description
				'%s', // status
				'%s', // updated_at
			),
			array( '%d' ) // WHERE id = %d
		);

		return $result !== false;
	}
	*/

	/**
	 * Delete a record.
	 *
	 * Removes a row from the database.
	 *
	 * BEGINNER'S NOTE:
	 * - $wpdb->delete() securely deletes database rows
	 * - First parameter: table name
	 * - Second parameter: WHERE clause (which rows to delete)
	 * - Third parameter: data type format for WHERE clause
	 * - Returns the number of rows deleted, or false on error
	 *
	 * @since 1.0.0
	 * @param int $id Record ID to delete.
	 * @return bool True on success, false on failure.
	 */
	/*
	public function delete( $id ) {
		global $wpdb;

		$result = $wpdb->delete(
			$this->main_table,
			array( 'id' => $id ),
			array( '%d' )
		);

		return $result !== false;
	}
	*/

	/**
	 * Get a single record by ID.
	 *
	 * Retrieves one row from the database by its ID.
	 *
	 * BEGINNER'S NOTE:
	 * - $wpdb->prepare() safely prepares SQL queries with user input
	 * - %d is a placeholder for integer values (IDs)
	 * - %s is a placeholder for string values
	 * - $wpdb->get_row() retrieves a single row as an object
	 * - OBJECT return type means you access data like: $result->column_name
	 *
	 * @since 1.0.0
	 * @param int $id Record ID.
	 * @return object|null Database row object or null if not found.
	 */
	/*
	public function get( $id ) {
		global $wpdb;

		$query = $wpdb->prepare(
			"SELECT * FROM {$this->main_table} WHERE id = %d",
			$id
		);

		return $wpdb->get_row( $query, OBJECT );
	}
	*/

	/**
	 * Get all records.
	 *
	 * Retrieves all rows from the database with optional filtering and pagination.
	 *
	 * BEGINNER'S NOTE:
	 * - This method demonstrates how to build dynamic queries with filters
	 * - wp_parse_args() merges user-provided args with defaults
	 * - LIMIT/OFFSET are used for pagination (show 20 items per page, etc.)
	 * - Always sanitize and validate user input before using in queries
	 *
	 * @since 1.0.0
	 * @param array $args {
	 *     Optional. Query arguments.
	 *
	 *     @type int    $limit   Maximum number of records to retrieve. Default 20.
	 *     @type int    $offset  Number of records to skip. Default 0.
	 *     @type string $orderby Column to order by. Default 'created_at'.
	 *     @type string $order   Sort order: ASC or DESC. Default 'DESC'.
	 *     @type string $status  Filter by status. Default empty (all statuses).
	 * }
	 * @return array Array of database row objects.
	 */
	/*
	public function get_all( $args = array() ) {
		global $wpdb;

		$defaults = array(
			'limit'   => 20,
			'offset'  => 0,
			'orderby' => 'created_at',
			'order'   => 'DESC',
			'status'  => '',
		);

		$args = wp_parse_args( $args, $defaults );

		// Build WHERE clause.
		$where = '1=1'; // Always true, allows adding conditions with AND.
		
		if ( ! empty( $args['status'] ) ) {
			$where .= $wpdb->prepare( ' AND status = %s', $args['status'] );
		}

		// Sanitize ORDER BY and ORDER to prevent SQL injection.
		$orderby = in_array( $args['orderby'], array( 'id', 'created_at', 'updated_at', 'title' ), true ) 
			? $args['orderby'] 
			: 'created_at';
		
		$order = 'DESC' === strtoupper( $args['order'] ) ? 'DESC' : 'ASC';

		// Build final query.
		$query = "SELECT * FROM {$this->main_table} 
				  WHERE {$where} 
				  ORDER BY {$orderby} {$order} 
				  LIMIT %d OFFSET %d";

		$prepared_query = $wpdb->prepare( $query, $args['limit'], $args['offset'] );

		return $wpdb->get_results( $prepared_query, OBJECT );
	}
	*/

	/**
	 * Count records.
	 *
	 * Gets the total number of records, optionally filtered by criteria.
	 *
	 * BEGINNER'S NOTE:
	 * - COUNT(*) is an SQL function that counts rows
	 * - $wpdb->get_var() retrieves a single value from the database
	 * - Useful for pagination (total pages = total records / records per page)
	 *
	 * @since 1.0.0
	 * @param array $args Optional. Filter arguments (status, etc.).
	 * @return int Number of records.
	 */
	/*
	public function count( $args = array() ) {
		global $wpdb;

		$where = '1=1';
		
		if ( ! empty( $args['status'] ) ) {
			$where .= $wpdb->prepare( ' AND status = %s', $args['status'] );
		}

		$query = "SELECT COUNT(*) FROM {$this->main_table} WHERE {$where}";

		return (int) $wpdb->get_var( $query );
	}
	*/
}
