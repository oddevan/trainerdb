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
class PokemonType extends Taxonomy {
	/**
	 * Permalink slug for this taxonomy
	 *
	 * @var string $slug Permalink prefix
	 * @since 1.0.0
	 */
	protected $slug = 'pokemon_type';

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
			'name'                       => _x( 'Pokémon Types', 'Taxonomy General Name', 'trainerdb' ),
			'singular_name'              => _x( 'Pokémon Type', 'Taxonomy Singular Name', 'trainerdb' ),
			'menu_name'                  => __( 'Pokémon Types', 'trainerdb' ),
			'all_items'                  => __( 'All Pokémon Types', 'trainerdb' ),
			'parent_item'                => __( 'Parent Pokémon Type', 'trainerdb' ),
			'parent_item_colon'          => __( 'Parent Pokémon Type:', 'trainerdb' ),
			'new_item_name'              => __( 'New Pokémon Types', 'trainerdb' ),
			'add_new_item'               => __( 'Add New Pokémon Type', 'trainerdb' ),
			'edit_item'                  => __( 'Edit Pokémon Type', 'trainerdb' ),
			'update_item'                => __( 'Update Pokémon Type', 'trainerdb' ),
			'view_item'                  => __( 'View Pokémon Type', 'trainerdb' ),
			'separate_items_with_commas' => __( 'Separate items with commas', 'trainerdb' ),
			'add_or_remove_items'        => __( 'Add or remove Pokémon types', 'trainerdb' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'trainerdb' ),
			'popular_items'              => __( 'Popular Pokémon Types', 'trainerdb' ),
			'search_items'               => __( 'Search Pokémon Types', 'trainerdb' ),
			'not_found'                  => __( 'Not Found', 'trainerdb' ),
			'no_terms'                   => __( 'No Pokémon types', 'trainerdb' ),
			'items_list'                 => __( 'Pokémon Types list', 'trainerdb' ),
			'items_list_navigation'      => __( 'Pokémon Types list navigation', 'trainerdb' ),
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
			'hierarchical' => true,
		];
	}
}
