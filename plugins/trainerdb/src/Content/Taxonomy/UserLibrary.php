<?php //phpcs:ignore Wordpress.Files.Filename
/**
 * Custom taxonomy to store cards for users.
 *
 * @since 0.1.0
 * @package oddEvan\TrainerDB
 */

namespace oddEvan\TrainerDB\Content\Taxonomy;

use WebDevStudios\OopsWP\Structure\Content\Taxonomy;

/**
 * Custom taxonomy Type for user libraries. The slug for the taxonomy
 * should correspond to a username.
 *
 * @since 1.0.0
 */
class UserLibrary extends Taxonomy {
	/**
	 * Permalink slug for this taxonomy
	 *
	 * @var string $slug Permalink prefix
	 * @since 1.0.0
	 */
	protected $slug = 'user_library';

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
			'name'                       => _x( 'User Libraries', 'Taxonomy General Name', 'trainerdb' ),
			'singular_name'              => _x( 'User Library', 'Taxonomy Singular Name', 'trainerdb' ),
			'menu_name'                  => __( 'User Library', 'trainerdb' ),
			'all_items'                  => __( 'All User Libraries', 'trainerdb' ),
			'parent_item'                => __( 'Parent User Library', 'trainerdb' ),
			'parent_item_colon'          => __( 'Parent User Library:', 'trainerdb' ),
			'new_item_name'              => __( 'New User Libraries', 'trainerdb' ),
			'add_new_item'               => __( 'Add New User Library', 'trainerdb' ),
			'edit_item'                  => __( 'Edit User Library', 'trainerdb' ),
			'update_item'                => __( 'Update User Library', 'trainerdb' ),
			'view_item'                  => __( 'View User Library', 'trainerdb' ),
			'separate_items_with_commas' => __( 'Separate items with commas', 'trainerdb' ),
			'add_or_remove_items'        => __( 'Add or remove user libraries', 'trainerdb' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'trainerdb' ),
			'popular_items'              => __( 'Popular User Libraries', 'trainerdb' ),
			'search_items'               => __( 'Search User Libraries', 'trainerdb' ),
			'not_found'                  => __( 'Not Found', 'trainerdb' ),
			'no_terms'                   => __( 'No User Libraries', 'trainerdb' ),
			'items_list'                 => __( 'User Libraries list', 'trainerdb' ),
			'items_list_navigation'      => __( 'User Libraries list navigation', 'trainerdb' ),
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
