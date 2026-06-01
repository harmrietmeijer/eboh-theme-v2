<?php
/**
 * Template Name: Programma & Uitslagen
 * Template Post Type: page
 *
 * @package EBOH
 * @since 2.0.0
 */

get_header();
$hero_image = get_the_post_thumbnail_url( get_the_ID(), 'full' );
?>

<section class="page-hero<?php echo $hero_image ? ' page-hero--image' : ''; ?>"
	<?php if ( $hero_image ) : ?>style="background-image: url('<?php echo esc_url( $hero_image ); ?>');"<?php endif; ?>>
	<div class="page-hero__container">
		<p class="page-hero__breadcrumbs"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> / <?php the_title(); ?></p>
		<span class="page-hero__eyebrow"><?php esc_html_e( 'Wedstrijden & uitslagen', 'eboh' ); ?></span>
		<h1 class="page-hero__title"><?php the_title(); ?></h1>
		<?php if ( get_the_excerpt() ) : ?>
			<p class="page-hero__subtitle"><?php echo esc_html( get_the_excerpt() ); ?></p>
		<?php endif; ?>
	</div>
</section>

<main id="main" class="page-shell">
	<div class="page-container">
		<?php if ( get_the_content() ) : ?>
			<div class="page-content"><?php the_content(); ?></div>
		<?php else : ?>
			<p style="opacity:0.7;font-size:15px;"><?php esc_html_e( 'Voeg via de pagina-editor het programma en de uitslagen toe (bijv. via een KNVB widget of shortcode).', 'eboh' ); ?></p>
		<?php endif; ?>
	</div>
</main>

<?php get_footer();
