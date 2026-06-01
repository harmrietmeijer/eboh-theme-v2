<?php
/**
 * Archive Template for EBOH
 *
 * @package EBOH
 * @since 2.0.0
 */

get_header();
?>

<?php
// Bepaal hero-tekst per archief-type, zodat we niet overal het generieke 'Archief /
// Archieven: …' label tonen.
$hero_title    = '';
$hero_eyebrow  = '';
$hero_crumb    = '';
if ( is_post_type_archive( 'team' ) ) {
	$hero_title   = __( 'Teams', 'eboh' );
	$hero_eyebrow = __( 'Onze teams', 'eboh' );
	$hero_crumb   = __( 'Teams', 'eboh' );
} else {
	// Strip 'Archieven: ' / 'Archive: ' prefix uit de standaard archive-titel.
	$default_title = wp_strip_all_tags( get_the_archive_title() );
	$hero_title    = preg_replace( '/^(Archieven:|Archive:)\s*/i', '', $default_title );
	$hero_eyebrow  = __( 'Overzicht', 'eboh' );
	$hero_crumb    = $hero_title;
}
?>
<section class="page-hero">
	<div class="page-hero__container">
		<p class="page-hero__breadcrumbs"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> / <?php echo esc_html( $hero_crumb ); ?></p>
		<span class="page-hero__eyebrow"><?php echo esc_html( $hero_eyebrow ); ?></span>
		<h1 class="page-hero__title"><?php echo esc_html( $hero_title ); ?></h1>
		<?php the_archive_description( '<p class="page-hero__subtitle">', '</p>' ); ?>
	</div>
</section>

<main id="main" class="page-shell page-shell--muted">
	<div class="page-container">
		<?php
		if ( have_posts() ) :
			?>
			<div class="news-grid" style="grid-template-columns:repeat(auto-fill,minmax(300px,1fr));">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'parts/news-card' );
				endwhile;
				?>
			</div>

			<div style="margin-top:48px;text-align:center;">
				<?php
				the_posts_pagination( array(
					'prev_text' => esc_html__( '← Vorige', 'eboh' ),
					'next_text' => esc_html__( 'Volgende →', 'eboh' ),
				) );
				?>
			</div>
		<?php else : ?>
			<p><?php esc_html_e( 'Geen artikelen gevonden.', 'eboh' ); ?></p>
		<?php endif; ?>
	</div>
</main>

<?php get_footer();
