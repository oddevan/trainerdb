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
class CardHash extends Taxonomy {
	/**
	 * Permalink slug for this taxonomy
	 *
	 * @var string $slug Permalink prefix
	 * @since 1.0.0
	 */
	protected $slug = 'card_hash';

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
			'name'                       => _x( 'Hashes', 'Taxonomy General Name', 'trainerdb' ),
			'singular_name'              => _x( 'Hash', 'Taxonomy Singular Name', 'trainerdb' ),
			'menu_name'                  => __( 'Hash', 'trainerdb' ),
			'all_items'                  => __( 'All Hashes', 'trainerdb' ),
			'parent_item'                => __( 'Parent Hash', 'trainerdb' ),
			'parent_item_colon'          => __( 'Parent Hash:', 'trainerdb' ),
			'new_item_name'              => __( 'New Hash Name', 'trainerdb' ),
			'add_new_item'               => __( 'Add New Hash', 'trainerdb' ),
			'edit_item'                  => __( 'Edit Hash', 'trainerdb' ),
			'update_item'                => __( 'Update Hash', 'trainerdb' ),
			'view_item'                  => __( 'View Hash', 'trainerdb' ),
			'separate_items_with_commas' => __( 'Separate hashes with commas', 'trainerdb' ),
			'add_or_remove_items'        => __( 'Add or remove hashes', 'trainerdb' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'trainerdb' ),
			'popular_items'              => __( 'Popular Hashes', 'trainerdb' ),
			'search_items'               => __( 'Search Hashes', 'trainerdb' ),
			'not_found'                  => __( 'Not Found', 'trainerdb' ),
			'no_terms'                   => __( 'No hashes', 'trainerdb' ),
			'items_list'                 => __( 'Hashes list', 'trainerdb' ),
			'items_list_navigation'      => __( 'Hashes list navigation', 'trainerdb' ),
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
			'hierarchical' => false,
		];
	}
}
