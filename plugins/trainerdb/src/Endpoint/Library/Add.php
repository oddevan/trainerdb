<?php //phpcs:ignore Wordpress.Files.Filename
/**
 * Content Registrar for the plugin
 *
 * @since 0.1.0
 * @package oddEvan\TrainerDB
 */

namespace oddEvan\TrainerDB\Endpoint\Library;

use WebDevStudios\OopsWP\Structure\Content\ApiEndpoint;
use oddEvan\TrainerDB\Database\LibraryHelper;

/**
 * Registrar class to register our custom post types
 *
 * @since 0.1.0
 */
class Add extends ApiEndpoint {
	private $library_helper = null;

	public function __construct() {
		$this->library_helper = new LibraryHelper();
	}

	/**
	 * The endpoint namespace
	 *
	 * @var string
	 * @since  0.1.0
	 */
	protected $namespace = 'trainerdb/v1';

	/**
	 * The endpoint route
	 *
	 * @var string
	 * @since  0.1.0
	 */
	protected $route = '/library/add';

	/**
	 * Get the arguments for this post type.
	 *
	 * Extending classes can override this method to pass in their own customizations specific to the endpoint.
	 *
	 * @author Evan Hildreth <evan.hildreth@webdevstudios.com>
	 * @since  0.1.0
	 * @return array
	 */
	protected function get_args() : array {
		return [
			'methods' => \WP_REST_Server::CREATABLE,
		];
	}

	/**
	 * This is our callback function that embeds our phrase in a WP_REST_Response
	 *
	 * @return WP_REST_Response
	 */
	public function run( $request = null ) {
		$data    = $request->get_json_params();
		$results = [];

		if ( isset( $data['cards'] ) && is_array( $data['cards'] ) ) {
			foreach ( $data['cards'] as $card_id => $quantity ) {
				$new_quantity        = $this->library_helper->adjust_quantity( 1, $card_id, $quantity );
				$results[ $card_id ] = $new_quantity;
			}
		}
		return new \WP_REST_Response( $results, 200 );
	}
}
