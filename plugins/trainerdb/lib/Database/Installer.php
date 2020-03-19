<?php //phpcs:ignore Wordpress.Files.Filename
/**
 * Class Installer for TrainerDB
 *
 * @since 0.1.0
 * @package oddEvan\TrainerDB
 */

namespace oddEvan\TrainerDB\Database;

/**
 * Create and maintain the custom database table for TrainerDB
 *
 * @since 0.1.0
 */
class Installer {
	private $db_version = 1;

	public function create_library_table() {
		global $wpdb;

		$table_name      = $wpdb->prefix . 'tdb_library';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			user_id bigint(20) NOT NULL,
			card_id varchar(10) NOT NULL,
			quantity int(5) NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

		update_option( 'trainerdb_db_version', $this->db_version );
	}
}
