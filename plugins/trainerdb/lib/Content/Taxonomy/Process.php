<?php //phpcs:ignore Wordpress.Files.Filename
/**
 * Custom taxonomy for internal processing flagging.
 *
 * @since 0.1.0
 * @package oddEvan\TrainerDB
 */

namespace oddEvan\TrainerDB\Content\Taxonomy;

use WebDevStudios\OopsWP\Structure\Content\Taxonomy;

/**
 * Custom taxonomy for internal processing flagging.
 *
 * @since 1.0.0
 */
class Process extends Taxonomy {
	/**
	 * Permalink slug for this taxonomy
	 *
	 * @var string $slug Permalink prefix
	 * @since 1.0.0
	 */
	protected $slug = 'process';

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
			'name'                       => _x( 'Processes', 'Taxonomy General Name', 'trainerdb' ),
			'singular_name'              => _x( 'Process', 'Taxonomy Singular Name', 'trainerdb' ),
			'menu_name'                  => __( 'Processes', 'trainerdb' ),
			'all_items'                  => __( 'All Processes', 'trainerdb' ),
			'parent_item'                => __( 'Parent Process', 'trainerdb' ),
			'parent_item_colon'          => __( 'Parent Process:', 'trainerdb' ),
			'new_item_name'              => __( 'New Processes', 'trainerdb' ),
			'add_new_item'               => __( 'Add New Process', 'trainerdb' ),
			'edit_item'                  => __( 'Edit Process', 'trainerdb' ),
			'update_item'                => __( 'Update Process', 'trainerdb' ),
			'view_item'                  => __( 'View Process', 'trainerdb' ),
			'separate_items_with_commas' => __( 'Separate items with commas', 'trainerdb' ),
			'add_or_remove_items'        => __( 'Add or remove processes', 'trainerdb' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'trainerdb' ),
			'popular_items'              => __( 'Popular Processes', 'trainerdb' ),
			'search_items'               => __( 'Search Processes', 'trainerdb' ),
			'not_found'                  => __( 'Not Found', 'trainerdb' ),
			'no_terms'                   => __( 'No processes', 'trainerdb' ),
			'items_list'                 => __( 'Processes list', 'trainerdb' ),
			'items_list_navigation'      => __( 'Processes list navigation', 'trainerdb' ),
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
