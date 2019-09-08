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
class ACF extends Service {

	/**
	 * Called by Plugin class; register the hooks for this plugin
	 *
	 * @since 0.1.0
	 * @author me@eph.me
	 */
	public function register_hooks() {
		add_action( 'acf/init', [ $this, 'add_set_acf_fields' ] );
	}

	/**
	 * Register the ACF fields for the Set taxonomy
	 *
	 * @since 0.1.0
	 * @author me@eph.me
	 */
	public function add_set_acf_fields() {
		acf_add_local_field_group(array(
			'key'                   => 'group_5d69c7e39a32d',
			'title'                 => 'Sets',
			'fields'                => array(
				array(
					'key'   => 'field_5d69c7eb31011',
					'label' => 'PokemonTCG ID',
					'name'  => 'ptcg_id',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_5d69c80831012',
					'label' => 'TCGPlayer ID',
					'name'  => 'tcgp_id',
					'type'  => 'text',
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'taxonomy',
						'operator' => '==',
						'value'    => 'set',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
		));
	}
}
