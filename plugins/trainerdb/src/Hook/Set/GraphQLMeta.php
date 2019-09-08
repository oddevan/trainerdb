<?php //phpcs:ignore Wordpress.Files.Filename
/**
 * Content Registrar for the plugin
 *
 * @since 0.1.0
 * @package oddEvan\TrainerDB
 */

namespace oddEvan\TrainerDB\Hook\Set;

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
		add_action( 'graphql_register_types', [ $this, 'graphql_add_set_meta' ] );
	}

	/**
	 * Register the meta fields for the Card post type
	 *
	 * @since 0.1.0
	 * @author me@eph.me
	 */
	public function graphql_add_set_meta() {
		register_graphql_field( 'Set', 'pokemonID', [
			'type'        => 'String',
			'description' => __( 'The ID for the Pokemon TCG Developers service', 'trainerdb' ),
			'resolve'     => function( $post ) {
				$ptcg_id = get_post_meta( $post->ID, 'ptcg_id', true );
				return ! empty( $ptcg_id ) ? $ptcg_id : null;
			},
		] );

		register_graphql_field( 'Set', 'tcgPlayerID', [
			'type'        => 'String',
			'description' => __( 'The ID for this set on TCGplayer', 'trainerdb' ),
			'resolve'     => function( $post ) {
				$tcgp_id = get_post_meta( $post->ID, 'tcgp_id', true );
				return ! empty( $tcgp_id ) ? $tcgp_id : null;
			},
		] );
	}
}
