<?php //phpcs:ignore Wordpress.Files.Filename
/**
 * Content Registrar for the plugin
 *
 * @since 0.1.0
 * @package oddEvan\TrainerDB
 */

namespace oddEvan\TrainerDB\Page;

use WebDevStudios\OopsWP\Structure\Service;

/**
 * Registrar class to register our custom post types
 *
 * @since 0.1.0
 */
class Library extends Service {

	/**
	 * Called by Plugin class; register the hooks for this plugin
	 *
	 * @since 0.1.0
	 * @author me@eph.me
	 */
	public function register_hooks() {
		add_action( 'admin_menu', [ $this, 'admin_menu' ] );
		add_action( 'admin_post_trainerdb_addtolibrary', [ $this, 'post_add_to_library' ] );
	}

	/**
	 * Register the meta fields for the Card post type
	 *
	 * @since 0.1.0
	 * @author me@eph.me
	 */
	public function admin_menu() {
		add_menu_page(
			'My Library',
			'Library',
			'manage_options',
			'trainerdb_library',
			[ $this, 'admin_page' ],
			'dashicons-images-alt',
			6
		);

		add_submenu_page(
			'trainerdb_library',
			'Add cards to library',
			'Add cards',
			'manage_options',
			'trainerdb_library_add',
			[ $this, 'add_page' ],
			1
		);
	}

	public function admin_page() {
		?>
		<div class="wrap">
			<h1 class="wp-heading-inline">Card Library</h1>
			<a href="/wp-admin/admin.php?page=trainerdb_library_add" class="page-title-action">Add To Library</a>
			<hr class="wp-header-end">
		</div>
		<?php
	}

	public function add_page() {
		?>
		<div class="wrap">
			<h1 class="wp-heading-inline">Add Cards to Library</h1>
			<hr class="wp-header-end">
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="trainerdb_addtolibrary">
				<?php wp_nonce_field( 'trainerdb_library_add' ); ?>
				<?php wp_dropdown_categories( [ 'taxonomy' => 'set', 'hide_if_empty' => false ] ); ?>
				<button type="submit">Save</button>
			</form>
		</div>
		<?php
	}

	public function post_add_to_library() {
		check_admin_referer( 'trainerdb_library_add' );
		wp_safe_redirect( admin_url( 'admin.php?page=trainerdb_library' ) );
	}
}
