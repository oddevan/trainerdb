<?php //phpcs:ignore Wordpress.Files.Filename
/**
 * Custom taxonomy to store pokemon type (Fire, Water, etc)
 *
 * @since 0.1.0
 * @package oddEvan\TrainerDB
 */

namespace oddEvan\TrainerDB\Content\Taxonomy;

use WebDevStudios\OopsWP\Structure\Content\Taxonomy;

/**
 * Custom taxonomy Type for card sets
 *
 * @since 1.0.0
 */
class EnergyType extends Taxonomy {
	/**
	 * Permalink slug for this taxonomy
	 *
	 * @var string $slug Permalink prefix
	 * @since 1.0.0
	 */
	protected $slug = 'energy_type';

	/**
	 * Post types this taxonomy is valid for
	 *
	 * @var Array $object_types post type slugs
	 * @since 1.0.0
	 */
	protected $object_types = [ 'card' ];

	/**
	 * Override the superclass method and provide the labels array
	 * for registering the Lesson Category taxonomy
	 *
	 * @return Array labels for taxonomy.
	 * @author evan.hildreth@webdevstudios.com
	 * @since 1.0.0
	 */
	protected function get_labels() : array {
		return [
			'name'                       => _x( 'Energy Types', 'Taxonomy General Name', 'trainerdb' ),
			'singular_name'              => _x( 'Energy Type', 'Taxonomy Singular Name', 'trainerdb' ),
			'menu_name'                  => __( 'Energy Types', 'trainerdb' ),
			'all_items'                  => __( 'All Energy Types', 'trainerdb' ),
			'parent_item'                => __( 'Parent Energy Type', 'trainerdb' ),
			'parent_item_colon'          => __( 'Parent Energy Type:', 'trainerdb' ),
			'new_item_name'              => __( 'New Energy Types', 'trainerdb' ),
			'add_new_item'               => __( 'Add New Energy Type', 'trainerdb' ),
			'edit_item'                  => __( 'Edit Energy Type', 'trainerdb' ),
			'update_item'                => __( 'Update Energy Type', 'trainerdb' ),
			'view_item'                  => __( 'View Energy Type', 'trainerdb' ),
			'separate_items_with_commas' => __( 'Separate items with commas', 'trainerdb' ),
			'add_or_remove_items'        => __( 'Add or remove Energy types', 'trainerdb' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'trainerdb' ),
			'popular_items'              => __( 'Popular Energy Types', 'trainerdb' ),
			'search_items'               => __( 'Search Energy Types', 'trainerdb' ),
			'not_found'                  => __( 'Not Found', 'trainerdb' ),
			'no_terms'                   => __( 'No Energy types', 'trainerdb' ),
			'items_list'                 => __( 'Energy Types list', 'trainerdb' ),
			'items_list_navigation'      => __( 'Energy Types list navigation', 'trainerdb' ),
		];
	}

	/**
	 * Override the superclass method and provide the args array
	 * for registering the Lesson Category taxonomy
	 *
	 * @return Array information for post type.
	 * @author evan.hildreth@webdevstudios.com
	 * @since 1.0.0
	 */
	protected function get_args() : array {
		return [
			'hierarchical'        => true,
			'show_in_graphql'     => true,
			'graphql_single_name' => 'EnergyType',
			'graphql_plural_name' => 'EnergyTypes',
		];
	}
}
