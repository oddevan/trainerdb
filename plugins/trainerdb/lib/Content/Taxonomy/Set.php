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
 * Custom taxonomy Type for card sets
 *
 * @since 1.0.0
 */
class Set extends Taxonomy {
	/**
	 * Permalink slug for this taxonomy
	 *
	 * @var string $slug Permalink prefix
	 * @since 1.0.0
	 */
	protected $slug = 'set';

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
			'name'                       => _x( 'Sets', 'Taxonomy General Name', 'trainerdb' ),
			'singular_name'              => _x( 'Set', 'Taxonomy Singular Name', 'trainerdb' ),
			'menu_name'                  => __( 'Sets', 'trainerdb' ),
			'all_items'                  => __( 'All Sets', 'trainerdb' ),
			'parent_item'                => __( 'Parent Set', 'trainerdb' ),
			'parent_item_colon'          => __( 'Parent Set:', 'trainerdb' ),
			'new_item_name'              => __( 'New Sets', 'trainerdb' ),
			'add_new_item'               => __( 'Add New Set', 'trainerdb' ),
			'edit_item'                  => __( 'Edit Set', 'trainerdb' ),
			'update_item'                => __( 'Update Set', 'trainerdb' ),
			'view_item'                  => __( 'View Set', 'trainerdb' ),
			'separate_items_with_commas' => __( 'Separate items with commas', 'trainerdb' ),
			'add_or_remove_items'        => __( 'Add or remove sets', 'trainerdb' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'trainerdb' ),
			'popular_items'              => __( 'Popular Sets', 'trainerdb' ),
			'search_items'               => __( 'Search Sets', 'trainerdb' ),
			'not_found'                  => __( 'Not Found', 'trainerdb' ),
			'no_terms'                   => __( 'No sets', 'trainerdb' ),
			'items_list'                 => __( 'Sets list', 'trainerdb' ),
			'items_list_navigation'      => __( 'Sets list navigation', 'trainerdb' ),
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
			'graphql_single_name' => 'Set',
			'graphql_plural_name' => 'Sets',
		];
	}
}
