<?php //phpcs:ignore Wordpress.Files.Filename
/**
 * Custom Post Type for storing social connection (Twitter, Tumblr, etc.) information
 *
 * @since 0.1.0
 * @package oddEvan\TrainerDB
 */

namespace oddEvan\TrainerDB\Content\Taxonomy;

use WebDevStudios\OopsWP\Structure\Content\Taxonomy;

/**
 * Custom taxonomy Rarity for card printing availablility
 *
 * @since 1.0.0
 */
class Rarity extends Taxonomy {
	/**
	 * Permalink slug for this taxonomy
	 *
	 * @var string $slug Permalink prefix
	 * @since 1.0.0
	 */
	protected $slug = 'rarity';

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
			'name'                       => _x( 'Rarities', 'Taxonomy General Name', 'trainerdb' ),
			'singular_name'              => _x( 'Rarity', 'Taxonomy Singular Name', 'trainerdb' ),
			'menu_name'                  => __( 'Rarities', 'trainerdb' ),
			'all_items'                  => __( 'All Rarities', 'trainerdb' ),
			'parent_item'                => __( 'Parent Rarity', 'trainerdb' ),
			'parent_item_colon'          => __( 'Parent Set:', 'trainerdb' ),
			'new_item_name'              => __( 'New Rarities', 'trainerdb' ),
			'add_new_item'               => __( 'Add New Rarity', 'trainerdb' ),
			'edit_item'                  => __( 'Edit Rarity', 'trainerdb' ),
			'update_item'                => __( 'Update Rarity', 'trainerdb' ),
			'view_item'                  => __( 'View Rarity', 'trainerdb' ),
			'separate_items_with_commas' => __( 'Separate items with commas', 'trainerdb' ),
			'add_or_remove_items'        => __( 'Add or remove rarities', 'trainerdb' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'trainerdb' ),
			'popular_items'              => __( 'Popular Rarities', 'trainerdb' ),
			'search_items'               => __( 'Search Rarities', 'trainerdb' ),
			'not_found'                  => __( 'Not Found', 'trainerdb' ),
			'no_terms'                   => __( 'No rarities', 'trainerdb' ),
			'items_list'                 => __( 'Rarities list', 'trainerdb' ),
			'items_list_navigation'      => __( 'Rarities list navigation', 'trainerdb' ),
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
			'graphql_single_name' => 'Rarity',
			'graphql_plural_name' => 'Rarities',
		];
	}
}
