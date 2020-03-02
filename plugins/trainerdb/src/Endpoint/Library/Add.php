<?php //phpcs:ignore Wordpress.Files.Filename
/**
 * Content Registrar for the plugin
 *
 * @since 0.1.0
 * @package oddEvan\TrainerDB
 */

namespace oddEvan\TrainerDB\Endpoint\Library;

use WebDevStudios\OopsWP\Structure\Content\ApiEndpoint;

/**
 * Registrar class to register our custom post types
 *
 * @since 0.1.0
 */
class Add extends ApiEndpoint {
	/**
	 * The endpoint namespace
	 *
	 * @var string
	 * @since 2020-01-30
	 */
	protected $namespace = 'trainerdb/v1';

	/**
	 * The endpoint route
	 *
	 * @var string
	 * @since 2020-01-30
	 */
	protected $route = '/library/add';

	/**
	 * This is our callback function that embeds our phrase in a WP_REST_Response
	 *
	 * @return WP_REST_Response
	 */
	public function run() {
		return rest_ensure_response( 'Hello World, this is the WordPress REST API' );
	}
}
