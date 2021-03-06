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
			$term_obj_list = get_the_terms( $post->ID, 'taxonomy' );
			$rarity_string = join( ', ', wp_list_pluck( $term_obj_list, 'name' ) );
			?>

			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<div class="post-thumbnail">
				<?php the_post_thumbnail(); ?>
			</div>
			<header class="entry-header">
				<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
			</header><!-- .entry-header -->
			<div class="entry-content">
				<div class="row">
					<div class="col-sm-8">
						<dl class="row">
							<dt class="col-sm-5">TrainerDB Code</dt>
							<dd class="col-sm-7"><code><?php echo esc_html( $post->post_name ); ?></code></dd>
							<dt class="col-sm-5">Rarity</dt>
							<dd class="col-sm-7">
								<?php echo esc_html( $rarity_string ); ?>
								<?php echo get_field( 'reverse_holographic' ) ? esc_html__( ' (Reverse Holographic)', 'trainerdb' ) : ''; ?>
							</dd>
							<dt class="col-sm-5">HP</dt>
							<dd class="col-sm-7"><code><?php echo $post->post_name; // phpcs:xss ok. ?></code></dd>
							<dt class="col-sm-5">Evolves From</dt>
							<dd class="col-sm-7"><code><?php echo $post->post_name; // phpcs:xss ok. ?></code></dd>
							<dt class="col-sm-5">Weakness</dt>
							<dd class="col-sm-7"><code><?php echo $post->post_name; // phpcs:xss ok. ?></code></dd>
							<dt class="col-sm-5">Resistance</dt>
							<dd class="col-sm-7"><code><?php echo $post->post_name; // phpcs:xss ok. ?></code></dd>
							<dt class="col-sm-5">Retreat Cost</dt>
							<dd class="col-sm-7"><code><?php echo $post->post_name; // phpcs:xss ok. ?></code></dd>
						</dl>

						<p><?php echo esc_html( get_field( 'card_text' ) ); ?></p>
					</div>
					<aside class="col-sm-4">
						<img src="<?php echo esc_url( get_field( 'image_url' ) ); ?>" alt="Image of card" class="img-responsive">
					</aside>
				</div>
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
