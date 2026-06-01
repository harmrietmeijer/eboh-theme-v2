<?php
/**
 * Template Name: Contact
 * Template Post Type: page
 *
 * @package EBOH
 * @since 2.0.0
 */

get_header();

$hero_image = get_the_post_thumbnail_url( get_the_ID(), 'full' );
$phone   = get_theme_mod( 'eboh_club_phone', '+31 (0)78 - 613 2834' );
$email   = get_theme_mod( 'eboh_club_email', 'info@eboh.nl' );
$address = get_theme_mod( 'eboh_club_address', 'Sportcomplex Schenkeldijk 6' );
$zip     = get_theme_mod( 'eboh_club_zipcode', '3328 LE' );
$city    = get_theme_mod( 'eboh_club_city', 'Dordrecht' );
?>

<section class="page-hero<?php echo $hero_image ? ' page-hero--image' : ''; ?>"
	<?php if ( $hero_image ) : ?>style="background-image: url('<?php echo esc_url( $hero_image ); ?>');"<?php endif; ?>>
	<div class="page-hero__container">
		<p class="page-hero__breadcrumbs"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> / <?php the_title(); ?></p>
		<span class="page-hero__eyebrow"><?php esc_html_e( 'Neem contact op', 'eboh' ); ?></span>
		<h1 class="page-hero__title"><?php the_title(); ?></h1>
		<?php if ( get_the_excerpt() ) : ?>
			<p class="page-hero__subtitle"><?php echo esc_html( get_the_excerpt() ); ?></p>
		<?php endif; ?>
	</div>
</section>

<main id="main" class="page-shell">
	<div class="page-container">
		<div class="two-col">
			<div>
				<?php if ( get_the_content() ) : ?>
					<div class="page-content"><?php the_content(); ?></div>
				<?php else : ?>
					<h2 class="page-section__title page-section__title--bar"><?php esc_html_e( 'Stuur een bericht', 'eboh' ); ?></h2>
					<p style="font-size:15px;line-height:1.7;opacity:0.8;"><?php esc_html_e( 'Voeg via de pagina-editor een formulier-shortcode toe (bijv. Contact Form 7) of plaats hier eigen tekst.', 'eboh' ); ?></p>
				<?php endif; ?>
			</div>

			<aside>
				<div class="info-card" style="margin-bottom:20px;">
					<h3 class="info-card__title"><?php esc_html_e( 'Clubgegevens', 'eboh' ); ?></h3>
					<ul class="info-card__list">
						<li><strong><?php esc_html_e( 'Adres', 'eboh' ); ?></strong><span><?php echo esc_html( $address ); ?><br><?php echo esc_html( $zip . ' ' . $city ); ?></span></li>
						<li><strong><?php esc_html_e( 'Telefoon', 'eboh' ); ?></strong><span><?php echo esc_html( $phone ); ?></span></li>
						<li><strong><?php esc_html_e( 'E-mail', 'eboh' ); ?></strong><span><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></span></li>
					</ul>
				</div>
			</aside>
		</div>
	</div>
</main>

<?php get_footer();
