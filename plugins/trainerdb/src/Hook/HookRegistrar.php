<?php //phpcs:ignore Wordpress.Files.Filename
/**
 * Content Registrar for the plugin
 *
 * @since 0.1.0
 * @package oddEvan\TrainerDB
 */

namespace oddEvan\TrainerDB\Hook;

use WebDevStudios\OopsWP\Structure\Service;

/**
 * Registrar class to register our custom post types
 *
 * @since 0.1.0
 */
class HookRegistrar extends Service {

	/**
	 * List of PostType classes that should be registered
	 * by this service
	 *
	 * @var Array $post_types array of PostType classes
	 * @since 0.1.0
	 */
	protected $subhooks = [
		Card\ACF::class,
		Card\GraphQLMeta::class,
		Set\ACF::class,
	];

	/**
	 * Called by Plugin class; register the hooks for this plugin
	 *
	 * @since 0.1.0
	 * @author me@eph.me
	 */
	public function register_hooks() {
		add_action( 'init', [ $this, 'register_subhooks' ] );
	}

	/**
	 * Iterate through $post_types and register them.
	 *
	 * @since 0.1.0
	 * @author me@eph.me
	 */
	public function register_subhooks() {
		foreach ( $this->subhooks as $hook_class ) {
			$hook_type = new $hook_class();
			$hook_type->register();
		}
	}
}
