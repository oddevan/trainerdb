<?php //phpcs:ignore Wordpress.Files.Filename
/**
 * Content Registrar for the plugin
 *
 * @since 0.1.0
 * @package oddEvan\TrainerDB
 */

namespace oddEvan\TrainerDB\Endpoint\Library;

use WebDevStudios\OopsWP\Structure\Service;

/**
 * Registrar class to register our custom post types
 *
 * @since 0.1.0
 */
class Add extends Service {

	/**
	 * Called by Plugin class; register the hooks for this plugin
	 *
	 * @since 0.1.0
	 * @author me@eph.me
	 */
	public function register_hooks() {
		add_action( 'rest_api_init', [ $this, 'register_route' ] );
	}

	/**
	 * Register the meta fields for the Card post type
	 *
	 * @since 0.1.0
	 * @author me@eph.me
	 */
	public function register_route() {
		register_rest_route(
			'trainerdb/v1',
			'/library/add',
			[
				'methods'  => \WP_REST_Server::READABLE,
				'callback' => [ $this, 'run' ],
			]
		);
	}

	/**
	 * This is our callback function that embeds our phrase in a WP_REST_Response
	 *
	 * @return WP_REST_Response
	 */
	public function run() {
		return rest_ensure_response( 'Hello World, this is the WordPress REST API' );
	}
}
