<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WP_Bootstrap_Starter
 */

global $post;

get_header(); ?>

	<section id="primary" class="content-area col-sm-12 col-lg-8">
		<main id="main" class="site-main" role="main">

		<?php
		while ( have_posts() ) :
			the_post();
			?>

			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<div class="post-thumbnail">
				<?php the_post_thumbnail(); ?>
			</div>
			<header class="entry-header">
				<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
			</header><!-- .entry-header -->
			<div class="entry-content row">
				<div class="col-sm-8">
				<code><?php echo $post->post_name; // phpcs:xss ok. ?></code>
				</div>
				<aside class="col-sm-4">
					<img src="<?php the_field( 'image_url' ); ?>" alt="Image of card" class="img-responsive">
				</aside>
			</div><!-- .entry-content -->

			<footer class="entry-footer">
				<?php wp_bootstrap_starter_entry_footer(); ?>
			</footer><!-- .entry-footer -->
		</article><!-- #post-## -->

						<?php
			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;

		endwhile; // End of the loop.
		?>

		</main><!-- #main -->
	</section><!-- #primary -->

<?php
get_sidebar();
get_footer();
