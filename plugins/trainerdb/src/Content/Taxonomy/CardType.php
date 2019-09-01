<?php //phpcs:ignore Wordpress.Files.Filename
/**
 * Custom taxonomy to store card type (Trainer/Pokémon, etc)
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
class CardType extends Taxonomy {
	/**
	 * Permalink slug for this taxonomy
	 *
	 * @var string $slug Permalink prefix
	 * @since 1.0.0
	 */
	protected $slug = 'card_type';

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
			'name'                       => _x( 'Card Types', 'Taxonomy General Name', 'trainerdb' ),
			'singular_name'              => _x( 'Card Type', 'Taxonomy Singular Name', 'trainerdb' ),
			'menu_name'                  => __( 'Card Types', 'trainerdb' ),
			'all_items'                  => __( 'All Card Types', 'trainerdb' ),
			'parent_item'                => __( 'Parent Card Type', 'trainerdb' ),
			'parent_item_colon'          => __( 'Parent Card Type:', 'trainerdb' ),
			'new_item_name'              => __( 'New Card Types', 'trainerdb' ),
			'add_new_item'               => __( 'Add New Card Type', 'trainerdb' ),
			'edit_item'                  => __( 'Edit Card Type', 'trainerdb' ),
			'update_item'                => __( 'Update Card Type', 'trainerdb' ),
			'view_item'                  => __( 'View Card Type', 'trainerdb' ),
			'separate_items_with_commas' => __( 'Separate items with commas', 'trainerdb' ),
			'add_or_remove_items'        => __( 'Add or remove card types', 'trainerdb' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'trainerdb' ),
			'popular_items'              => __( 'Popular Card Types', 'trainerdb' ),
			'search_items'               => __( 'Search Card Types', 'trainerdb' ),
			'not_found'                  => __( 'Not Found', 'trainerdb' ),
			'no_terms'                   => __( 'No card types', 'trainerdb' ),
			'items_list'                 => __( 'Card Types list', 'trainerdb' ),
			'items_list_navigation'      => __( 'Card Types list navigation', 'trainerdb' ),
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
