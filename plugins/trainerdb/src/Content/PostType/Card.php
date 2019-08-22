<?php //phpcs:ignore Wordpress.Files.Filename
/**
 * Custom Post Type for storing social connection (Twitter, Tumblr, etc.) information
 *
 * @since 0.1.0
 * @package oddEvan\TrainerDB
 */

namespace oddEvan\TrainerDB\Content\PostType;

use WebDevStudios\OopsWP\Structure\Content\PostType;

/**
 * Custom post type for social connections.
 *
 * @since 0.1.0
 */
class Card extends PostType {
	/**
	 * Permalink slug for this post type
	 *
	 * @var string $slug Permalink prefix
	 * @since 0.1.0
	 */
	protected $slug = 'card';

	/**
	 * Override the superclass method and provide the labels array
	 * for registering the Connection post type
	 *
	 * @return Array labels for post type.
	 * @author me@eph.me
	 * @since 0.1.0
	 */
	protected function get_labels() : array {
		return [
			'name'                  => _x( 'Cards', 'Post Type General Name', 'trainerdb' ),
			'singular_name'         => _x( 'Card', 'Post Type Singular Name', 'trainerdb' ),
			'menu_name'             => __( 'Cards', 'trainerdb' ),
			'name_admin_bar'        => __( 'Card', 'trainerdb' ),
			'archives'              => __( 'Card Archives', 'trainerdb' ),
			'attributes'            => __( 'Card Attributes', 'trainerdb' ),
			'parent_item_colon'     => __( 'Parent Card:', 'trainerdb' ),
			'all_items'             => __( 'All Cards', 'trainerdb' ),
			'add_new_item'          => __( 'Add New Card', 'trainerdb' ),
			'add_new'               => __( 'Add New', 'trainerdb' ),
			'new_item'              => __( 'New Card', 'trainerdb' ),
			'edit_item'             => __( 'Edit Card', 'trainerdb' ),
			'update_item'           => __( 'Update Card', 'trainerdb' ),
			'view_item'             => __( 'View Card', 'trainerdb' ),
			'view_items'            => __( 'View Cards', 'trainerdb' ),
			'search_items'          => __( 'Search Card', 'trainerdb' ),
			'not_found'             => __( 'Not found', 'trainerdb' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'trainerdb' ),
			'featured_image'        => __( 'Image', 'trainerdb' ),
			'set_featured_image'    => __( 'Set image', 'trainerdb' ),
			'remove_featured_image' => __( 'Remove image', 'trainerdb' ),
			'use_featured_image'    => __( 'Use as image', 'trainerdb' ),
			'insert_into_item'      => __( 'Insert into card', 'trainerdb' ),
			'uploaded_to_this_item' => __( 'Uploaded to this card', 'trainerdb' ),
			'items_list'            => __( 'Cards list', 'trainerdb' ),
			'items_list_navigation' => __( 'Cards list navigation', 'trainerdb' ),
			'filter_items_list'     => __( 'Filter cards list', 'trainerdb' ),
		];
	}

	/**
	 * Override the superclass method and provide the args array
	 * for registering the Connection post type
	 *
	 * @return Array information for post type.
	 * @author me@eph.me
	 * @since 0.1.0
	 */
	protected function get_args() : array {
		return [
			'label'           => __( 'Card', 'trainerdb' ),
			'description'     => __( 'A particular card', 'trainerdb' ),
			'supports'        => [ 'title', 'thumbnail', 'custom-fields' ],
			'hierarchical'    => false,
			'public'          => true,
			'menu_position'   => 5,
			'menu_icon'       => 'dashicons-share-alt',
			'capability_type' => 'post',
			'show_in_rest'    => true,
		];
	}
}
