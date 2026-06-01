<?php
/**
 * 404 Error Template for EBOH
 *
 * @package EBOH
 * @since 2.0.0
 */

get_header();
?>

<section class="page-hero">
	<div class="page-hero__container">
		<span class="page-hero__eyebrow">404</span>
		<h1 class="page-hero__title"><?php esc_html_e( 'Pagina niet gevonden', 'eboh' ); ?></h1>
		<p class="page-hero__subtitle"><?php esc_html_e( 'Sorry, de pagina die je zoekt bestaat niet (meer). Probeer een zoekopdracht of ga terug naar de homepagina.', 'eboh' ); ?></p>
	</div>
</section>

<main id="main" class="page-shell">
	<div class="page-container page-container--narrow" style="text-align:center;">
		<div style="margin-bottom:32px;"><?php get_search_form(); ?></div>
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn filled"><?php esc_html_e( 'Terug naar home', 'eboh' ); ?></a>
	</div>
</main>

<?php get_footer();
