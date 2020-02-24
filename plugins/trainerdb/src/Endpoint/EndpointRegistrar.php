<?php //phpcs:ignore Wordpress.Files.Filename
/**
 * Page Registrar for the plugin
 *
 * @since 0.1.0
 * @package oddEvan\TrainerDB
 */

namespace oddEvan\TrainerDB\Endpoint;

use WebDevStudios\OopsWP\Structure\Service;

/**
 * Registrar class to register our custom post types
 *
 * @since 0.1.0
 */
class EndpointRegistrar extends Service {

	/**
	 * List of Service classes that should be registered
	 * by this service
	 *
	 * @var Array $post_types array of PostType classes
	 * @since 0.1.0
	 */
	protected $endpoints = [
		Library\Add::class,
	];

	/**
	 * Called by Plugin class; iterate through subhooks and register them
	 *
	 * @since 0.1.0
	 * @author me@eph.me
	 */
	public function register_hooks() {
		foreach ( $this->endpoints as $hook_class ) {
			$hook_type = new $hook_class();
			$hook_type->register_hooks();
		}
	}
}
