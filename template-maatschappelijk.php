<?php
/**
 * Template Name: Maatschappelijk
 * Template Post Type: page
 *
 * Toont bovenaan de 3 nieuwste posts met de 'maatschappelijk'-categorie,
 * daaronder de page-content (admin kan tekst in WP-editor toevoegen).
 *
 * @package EBOH
 * @since 3.0.0
 */

get_header();

while ( have_posts() ) :
	the_post();
	$has_thumb = has_post_thumbnail();
	?>

	<section class="page-hero<?php echo $has_thumb ? ' page-hero--image' : ''; ?>"
		<?php if ( $has_thumb ) : ?>style="background-image: url('<?php echo esc_url( get_the_post_thumbnail_url( null, 'full' ) ); ?>');"<?php endif; ?>>
		<div class="page-hero__container">
			<p class="page-hero__breadcrumbs"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'eboh-v2' ); ?></a> / <?php the_title(); ?></p>
			<span class="page-hero__eyebrow"><?php esc_html_e( 'EBOH in de samenleving', 'eboh-v2' ); ?></span>
			<h1 class="page-hero__title"><?php the_title(); ?></h1>
		</div>
	</section>

	<main id="main" class="page-shell">
		<div class="page-container">

			<!-- Bovenaan: 3 uitgelichte maatschappelijk-posts -->
			<?php
			$ms_query = new WP_Query( array(
				'post_type'           => 'post',
				'posts_per_page'      => 3,
				'orderby'             => 'date',
				'order'               => 'DESC',
				'category_name'       => 'maatschappelijk',
				'ignore_sticky_posts' => true,
			) );
			if ( $ms_query->have_posts() ) :
				?>
				<div class="news-grid" style="margin-bottom:48px;">
					<?php while ( $ms_query->have_posts() ) : $ms_query->the_post(); ?>
						<?php get_template_part( 'parts/news-card' ); ?>
					<?php endwhile; ?>
				</div>
				<?php
				wp_reset_postdata();
			endif;
			?>

			<!-- Daaronder: page-content uit WP-editor -->
			<?php if ( get_the_content() ) : ?>
				<div class="page-content"><?php the_content(); ?></div>
			<?php endif; ?>
		</div>
	</main>

	<?php
endwhile;

get_footer();
