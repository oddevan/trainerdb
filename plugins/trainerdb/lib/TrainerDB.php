<?php //phpcs:ignore Wordpress.Files.Filename
/**
 * Main plugin class that instantiates the plugin classes.
 *
 * @since 0.1.0
 * @package oddEvan\TrainerDB
 */

namespace oddEvan\TrainerDB;

use WebDevStudios\OopsWP\Structure\Plugin\Plugin;

/**
 * Main plugin class that instantiates the services
 *
 * @since 0.1.0
 */
class TrainerDB extends Plugin {

	/**
	 * List the services required by this plugin. The superclass
	 * will take care of launching them.
	 *
	 * @var Array $services array of service classes
	 * @since 0.1.0
	 */
	protected $services = [
		Content\ContentRegistrar::class,
		Hook\HookRegistrar::class,
		Page\PageRegistrar::class,
		Endpoint\EndpointRegistrar::class,
	];

}
