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
class GraphQLMeta extends Service {

	/**
	 * Called by Plugin class; register the hooks for this plugin
	 *
	 * @since 0.1.0
	 * @author me@eph.me
	 */
	public function register_hooks() {
		add_action( 'graphql_register_types', [ $this, 'graphql_add_card_meta' ] );
	}

	/**
	 * Register the meta fields for the Card post type
	 *
	 * @since 0.1.0
	 * @author me@eph.me
	 */
	public function graphql_add_card_meta() {
		register_graphql_field( 'Card', 'number', [
			'type'        => 'String',
			'description' => __( 'The number of the card within the set', 'trainerdb' ),
			'resolve'     => function( $post ) {
				$number = get_post_meta( $post->ID, 'card_number', true );
				return ! empty( $number ) ? $number : 'blue';
			},
		] );
	}
}
