<?php //phpcs:ignore Wordpress.Files.Filename
/**
 * Content Registrar for the plugin
 *
 * @since 0.1.0
 * @package oddEvan\TrainerDB
 */

namespace oddEvan\TrainerDB\Hook\Card;

use WebDevStudios\OopsWP\Structure\Service;
use \Fieldmanager_Textfield;
use \Fieldmanager_Group;

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
		add_action( 'fm_post_card', [ $this, 'add_card_fm_fields' ] );
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
					'key'   => 'field_6e7d99581efe3',
					'label' => 'TCGPlayer URL',
					'name'  => 'tcgp_url',
					'type'  => 'url',
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
				array(
					'key'       => 'field_5d81875f8c7f6',
					'label'     => 'Card Text',
					'name'      => 'card_text',
					'type'      => 'textarea',
					'new_lines' => '',
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
					'key'   => 'field_5d818a04380f7',
					'label' => 'Evolves From',
					'name'  => 'evolves_from',
					'type'  => 'text',
				),
				array(
					'key'           => 'field_5d8188dc380f3',
					'label'         => 'Weakness Type',
					'name'          => 'weakness_type',
					'type'          => 'taxonomy',
					'taxonomy'      => 'pokemon_type',
					'field_type'    => 'select',
					'allow_null'    => 1,
					'add_term'      => 0,
					'save_terms'    => 0,
					'load_terms'    => 0,
					'return_format' => 'id',
				),
				array(
					'key'   => 'field_5d81891a380f4',
					'label' => 'Weakness Modifier',
					'name'  => 'weakness_mod',
					'type'  => 'text',
				),
				array(
					'key'           => 'field_5d81892f380f5',
					'label'         => 'Resistance Type',
					'name'          => 'resistance_type',
					'type'          => 'taxonomy',
					'taxonomy'      => 'pokemon_type',
					'field_type'    => 'select',
					'allow_null'    => 1,
					'add_term'      => 0,
					'save_terms'    => 0,
					'load_terms'    => 0,
					'return_format' => 'id',
				),
				array(
					'key'   => 'field_5d818943380f6',
					'label' => 'Resistance Modifier',
					'name'  => 'resistance_mod',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_5d6d997d28939',
					'label' => 'Retreat Cost',
					'name'  => 'retreat_cost',
					'type'  => 'number',
				),
			),
			'location'              => array(
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

	/**
	 * Add Attacks FieldManager fields. Not using ACF because they charge for this type of field
	 * and, well, I'm on a budget. For now.
	 *
	 * @since 0.1.0
	 * @author Evan Hildreth <me@eph.me>
	 */
	public function add_card_fm_fields() {
		$fm2 = new Fieldmanager_Group( array(
			'name'     => 'ability',
			'limit'    => 1,
			'label'    => 'Ability',
			'children' => array(
				'name' => new Fieldmanager_Textfield( 'Name' ),
				'text' => new Fieldmanager_Textfield( 'Text' ),
			),
		) );
		$fm2->add_meta_box( 'Ability', 'card' );

		$fm = new Fieldmanager_Group( array(
			'name'           => 'attacks',
			'limit'          => 0,
			'label'          => 'New Attack',
			'label_macro'    => array( 'Attack: %s', 'name' ),
			'add_more_label' => 'Add another attack',
			'children'       => array(
				'name'   => new Fieldmanager_Textfield( 'Name' ),
				'cost'   => new \Fieldmanager_Select( [
					'name'       => 'cost',
					'datasource' => new \Fieldmanager_Datasource_Term( [ 'taxonomy' => 'pokemon_type' ] ),
					'limit'      => 0,
				] ),
				'damage' => new Fieldmanager_Textfield( 'Base Damage' ),
				'text'   => new Fieldmanager_Textfield( 'Text' ),
			),
		) );
		$fm->add_meta_box( 'Attacks', 'card' );
	}
}
