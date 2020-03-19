<?php //phpcs:ignore Wordpress.Files.Filename
/**
 * Content Registrar for the plugin
 *
 * @since 0.1.0
 * @package oddEvan\TrainerDB
 */

namespace oddEvan\TrainerDB\Hook\Card;

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
		add_filter( 'manage_card_posts_columns', [ $this, 'add_card_columns_header' ] );
		add_filter( 'manage_card_posts_custom_column', [ $this, 'add_card_columns_data' ], 10, 3 );
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
	public function add_card_columns_header( $columns ) {
		unset( $columns['date'] );
		$columns['slug']  = __( 'Slug' );

		return $columns;
	}

	/**
	 * Populate the custom columns
	 *
	 * @since 0.1.0
	 * @author me@eph.me
	 *
	 * @param string $column_name Name of curent column.
	 * @param int    $post_id ID for the current row.
	 * @return string modified $content
	 */
	public function add_card_columns_data( $column_name, $post_id ) {
		global $post;

		switch ( $column_name ) {
			case 'slug':
				$content = $post->post_name;
				break;
		}

		echo esc_html( $content );
	}
}
