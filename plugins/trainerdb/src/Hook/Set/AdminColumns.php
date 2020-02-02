<?php //phpcs:ignore Wordpress.Files.Filename
/**
 * Content Registrar for the plugin
 *
 * @since 0.1.0
 * @package oddEvan\TrainerDB
 */

namespace oddEvan\TrainerDB\Hook\Set;

use WebDevStudios\OopsWP\Structure\Service;

/**
 * Registrar class to register our custom post types
 *
 * @since 0.1.0
 */
class AdminColumns extends Service {

	/**
	 * Called by Plugin class; register the hooks for this plugin
	 *
	 * @since 0.1.0
	 * @author me@eph.me
	 */
	public function register_hooks() {
		add_filter( 'manage_edit-set_columns', [ $this, 'add_set_columns_header' ] );
		add_filter( 'manage_set_custom_column', [ $this, 'add_set_columns_data' ], 10, 3 );
	}

	/**
	 * Register the custom columns for the Set taxonomy
	 *
	 * @since 0.1.0
	 * @author me@eph.me
	 *
	 * @param array $columns Current array of columns in WordPress admin.
	 * @return array modified $columns array
	 */
	public function add_set_columns_header( $columns ) {
		unset( $columns['description'] );

		$columns['prefix']  = __( 'Prefix' );
		$columns['ptcg_id'] = __( 'PTCG ID' );
		$columns['tcgp_id'] = __( 'TCGP ID' );

		return $columns;
	}

	/**
	 * Populate the custom columns
	 *
	 * @since 0.1.0
	 * @author me@eph.me
	 *
	 * @param string $content Content for current column.
	 * @param string $column_name Name of curent column.
	 * @param int    $term_id ID for the current row.
	 * @return string modified $content
	 */
	public function add_set_columns_data( $content, $column_name, $term_id ) {
		switch ( $column_name ) {
			case 'prefix':
				$content = get_term_meta( $term_id, 'prefix', true );
				break;
			case 'tcgp_id':
				$content = get_term_meta( $term_id, 'tcgp_id', true );
				break;
			case 'ptcg_id':
				$content = get_term_meta( $term_id, 'ptcg_id', true );
				break;
		}

		return $content;
	}
}
