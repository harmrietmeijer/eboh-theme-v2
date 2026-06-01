<?php
/**
 * Search Results Template for EBOH
 *
 * @package EBOH
 * @since 2.0.0
 */

get_header();
?>

<section class="page-hero">
	<div class="page-hero__container">
		<p class="page-hero__breadcrumbs"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> / <?php esc_html_e( 'Zoekresultaten', 'eboh' ); ?></p>
		<span class="page-hero__eyebrow"><?php esc_html_e( 'Zoekresultaten voor', 'eboh' ); ?></span>
		<h1 class="page-hero__title">"<?php echo esc_html( get_search_query() ); ?>"</h1>
	</div>
</section>

<main id="main" class="page-shell page-shell--muted">
	<div class="page-container">
		<?php if ( have_posts() ) : ?>
			<div class="news-grid" style="grid-template-columns:repeat(auto-fill,minmax(300px,1fr));">
				<?php while ( have_posts() ) : the_post(); get_template_part( 'parts/news-card' ); endwhile; ?>
			</div>
			<div style="margin-top:48px;text-align:center;">
				<?php the_posts_pagination( array( 'prev_text' => '← Vorige', 'next_text' => 'Volgende →' ) ); ?>
			</div>
		<?php else : ?>
			<p style="text-align:center;font-size:18px;opacity:0.7;padding:60px 20px;"><?php esc_html_e( 'Geen resultaten gevonden. Probeer een andere zoekterm.', 'eboh' ); ?></p>
		<?php endif; ?>
	</div>
</main>

<?php get_footer();
