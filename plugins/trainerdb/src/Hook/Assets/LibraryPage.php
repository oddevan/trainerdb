<?php //phpcs:ignore Wordpress.Files.Filename
/**
 * Content Registrar for the plugin
 *
 * @since 0.1.0
 * @package oddEvan\TrainerDB
 */

namespace oddEvan\TrainerDB\Hook\Assets;

use WebDevStudios\OopsWP\Structure\Service;

/**
 * Registrar class to register our custom post types
 *
 * @since 0.1.0
 */
class LibraryPage extends Service {

	/**
	 * Called by Plugin class; register the hooks for this plugin
	 *
	 * @since 0.1.0
	 * @author me@eph.me
	 */
	public function register_hooks() {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_library_assets' ] );
	}

	/**
	 * Register the javascript/css for the Library pages
	 *
	 * @since 0.1.0
	 * @author me@eph.me
	 *
	 * @param string $page_slug Current Admin page.
	 * @return void used as control structure only
	 */
	public function enqueue_library_assets( $page_slug ) {
		if ( 'library_page_trainerdb_library_add' !== $page_slug ) {
			return;
		}

		wp_enqueue_script( 
			'trainerdb-library-add-js',
			\oddEvan\TrainerDB\_get_plugin_url() . '/build/index.js',
			[ 'wp-element' ],
			filemtime( \oddEvan\TrainerDB\_get_plugin_directory() . '/build/index.js' ),
			true
		);
	}
}
