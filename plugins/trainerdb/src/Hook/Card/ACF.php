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
class ACF extends Service {

	/**
	 * Called by Plugin class; register the hooks for this plugin
	 *
	 * @since 0.1.0
	 * @author me@eph.me
	 */
	public function register_hooks() {
		add_action( 'acf/init', [ $this, 'add_card_acf_fields' ] );
		add_action( 'acf/init', [ $this, 'add_pokemon_acf_fields' ] );
	}

	/**
	 * Register the ACF fields for the Card post type
	 *
	 * @since 0.1.0
	 * @author me@eph.me
	 */
	public function add_card_acf_fields() {
		acf_add_local_field_group( array(
			'key'                   => 'group_5d6aea8e9c875',
			'title'                 => 'Cards',
			'fields'                => array(
				array(
					'key'   => 'field_5d6aea926f1de',
					'label' => 'Card Number',
					'name'  => 'card_number',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_5d6aeaa46f1df',
					'label' => 'Pokémon TCG Developers ID',
					'name'  => 'ptcg_id',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_5d6aeacb6f1e0',
					'label' => 'TCGPlayer ID',
					'name'  => 'tcgp_id',
					'type'  => 'text',
				),
				array(
					'key'           => 'field_5d6aead76f1e1',
					'label'         => 'Reverse Holographic',
					'name'          => 'reverse_holographic',
					'type'          => 'true_false',
					'default_value' => 0,
				),
				array(
					'key'   => 'field_5d6d99581cdc2',
					'label' => 'Image URL',
					'name'  => 'image_url',
					'type'  => 'url',
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'card',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
		) );
	}

	/**
	 * Register Pokémon-specific fields on the Card post type.
	 *
	 * @since 0.1.0
	 * @author me@eph.me
	 */
	public function add_pokemon_acf_fields() {
		acf_add_local_field_group(array(
			'key'                   => 'group_5d6d996a80791',
			'title'                 => 'Pokemon Data',
			'fields'                => array(
				array(
					'key'   => 'field_5d6d996e28938',
					'label' => 'HP',
					'name'  => 'hp',
					'type'  => 'number',
				),
				array(
					'key'   => 'field_5d6d997d28939',
					'label' => 'Retreat Cost',
					'name'  => 'retreat_cost',
					'type'  => 'number',
				),
			),
			'location' => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'card',
					),
					array(
						'param'    => 'post_taxonomy',
						'operator' => '==',
						'value'    => 'card_type:pokemon',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
		) );
	}
}
