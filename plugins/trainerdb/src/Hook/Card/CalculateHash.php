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
class CalculateHash extends Service {

	/**
	 * Called by Plugin class; register the hooks for this plugin
	 *
	 * @since 0.1.0
	 * @author me@eph.me
	 */
	public function register_hooks() {
		add_action( 'save_post_card', [ $this, 'calculate_card_hash' ], 50, 1 );
	}

	/**
	 * Register the meta fields for the Card post type
	 *
	 * @since 0.1.0
	 * @author me@eph.me
	 * @param int $post_id ID of the card that was just edited.
	 */
	public function calculate_card_hash( $post_id ) {
		$hash_data = [
			get_the_title( $post_id ),
			get_post_meta( $post_id, 'card_text', true ),
			get_post_meta( $post_id, 'hp', true ),
			get_post_meta( $post_id, 'evolves_from', true ),
			get_post_meta( $post_id, 'retreat_cost', true ),
			get_post_meta( $post_id, 'weakness_type', true ),
			get_post_meta( $post_id, 'weakness_mod', true ),
			get_post_meta( $post_id, 'resistance_type', true ),
			get_post_meta( $post_id, 'resistance_mod', true ),
			get_post_meta( $post_id, 'attacks', false ),
			get_the_terms( $post_id, 'card_type' ),
			get_the_terms( $post_id, 'pokemon_type' ),
		];

		remove_action( 'save_post_card', [ $this, 'calculate_card_hash' ], 50, 1 );
		wp_set_object_terms( $post_id, md5( wp_json_encode( $hash_data ) ), 'card_hash' );
		add_action( 'save_post_card', [ $this, 'calculate_card_hash' ], 50, 1 );
	}
}
