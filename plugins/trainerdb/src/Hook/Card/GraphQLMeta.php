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
			'type'        => 'Int',
			'description' => __( 'The number of the card within the set', 'trainerdb' ),
			'resolve'     => function( $post ) {
				$number = get_post_meta( $post->ID, 'card_number', true );
				return ! empty( $number ) ? $number : 0;
			},
		] );

		register_graphql_field( 'Card', 'pokemonID', [
			'type'        => 'String',
			'description' => __( 'The ID for the Pokemon TCG Developers service', 'trainerdb' ),
			'resolve'     => function( $post ) {
				$ptcg_id = get_post_meta( $post->ID, 'ptcg_id', true );
				return ! empty( $ptcg_id ) ? $ptcg_id : null;
			},
		] );

		register_graphql_field( 'Card', 'tcgPlayerSKU', [
			'type'        => 'String',
			'description' => __( 'The SKU for this card on TCGplayer', 'trainerdb' ),
			'resolve'     => function( $post ) {
				$tcgp_id = get_post_meta( $post->ID, 'tcgp_id', true );
				return ! empty( $tcgp_id ) ? $tcgp_id : null;
			},
		] );

		register_graphql_field( 'Card', 'reverseHolographic', [
			'type'        => 'Boolean',
			'description' => __( 'Whether the card is a reverse-holographic printing', 'trainerdb' ),
			'resolve'     => function( $post ) {
				$value = get_post_meta( $post->ID, 'reverse_holographic', true );
				return ! empty( $value ) ? $value : false;
			},
		] );

		register_graphql_field( 'Card', 'imageURL', [
			'type'        => 'String',
			'description' => __( 'URL of a high-resolution image of the card.', 'trainerdb' ),
			'resolve'     => function( $post ) {
				$value = get_post_meta( $post->ID, 'image_url', true );
				return ! empty( $value ) ? $value : '';
			},
		] );

		register_graphql_field( 'Card', 'hp', [
			'type'        => 'Int',
			'description' => __( 'Total hit points on this Pokemon', 'trainerdb' ),
			'resolve'     => function( $post ) {
				$value = get_post_meta( $post->ID, 'hp', true );
				return ! empty( $value ) ? $value : 0;
			},
		] );

		register_graphql_field( 'Card', 'retreatCost', [
			'type'        => 'Int',
			'description' => __( 'Retreat cost for this Pokemon', 'trainerdb' ),
			'resolve'     => function( $post ) {
				$value = get_post_meta( $post->ID, 'retreat_cost', true );
				return is_numeric( $value ) ? $value : null;
			},
		] );
	}
}
