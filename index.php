<?php
/**
 * Blog Listing Template for EBOH (gebruikt door WordPress als Posts page, bv. /nieuws)
 *
 * @package EBOH
 */

get_header();

// Wanneer 'Posts page' in Reading Settings is gekoppeld aan een echte pagina, hebben
// we toegang tot die pagina (titel, featured image, excerpt). Anders vallen we terug
// op generieke 'Nieuws'-tekst zodat de header niet leeg is.
$posts_page_id = (int) get_option( 'page_for_posts' );
$hero_image    = $posts_page_id ? get_the_post_thumbnail_url( $posts_page_id, 'full' ) : '';
$hero_title    = $posts_page_id ? get_the_title( $posts_page_id ) : __( 'Nieuws', 'eboh' );
$hero_excerpt  = $posts_page_id ? get_post_field( 'post_excerpt', $posts_page_id ) : '';
?>

<section class="page-hero<?php echo $hero_image ? ' page-hero--image' : ''; ?>"
	<?php if ( $hero_image ) : ?>style="background-image: url('<?php echo esc_url( $hero_image ); ?>');"<?php endif; ?>>
	<div class="page-hero__container">
		<p class="page-hero__breadcrumbs"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> / <?php echo esc_html( $hero_title ); ?></p>
		<span class="page-hero__eyebrow"><?php esc_html_e( 'Van de club', 'eboh' ); ?></span>
		<h1 class="page-hero__title"><?php echo esc_html( $hero_title ); ?></h1>
		<?php if ( $hero_excerpt ) : ?>
			<p class="page-hero__subtitle"><?php echo esc_html( $hero_excerpt ); ?></p>
		<?php endif; ?>
	</div>
</section>

<main id="main" class="page-shell page-shell--muted">
	<div class="page-container">
		<?php if ( have_posts() ) : ?>
			<div class="news-grid">
				<?php while ( have_posts() ) : the_post(); ?>
					<?php get_template_part( 'parts/news-card' ); ?>
				<?php endwhile; ?>
			</div>

			<?php
			the_posts_pagination(
				array(
					'prev_text' => esc_html__( 'Vorige', 'eboh' ),
					'next_text' => esc_html__( 'Volgende', 'eboh' ),
				)
			);
			?>
		<?php else : ?>
			<p><?php esc_html_e( 'Geen nieuwsberichten gevonden.', 'eboh' ); ?></p>
		<?php endif; ?>
	</div>
</main>

<?php
get_footer();
