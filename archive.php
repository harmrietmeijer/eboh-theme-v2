<?php
/**
 * Archive Template
 *
 * @package EBOH
 * @since 2.0.0
 */

get_header();

// Hero-tekst per archief-type — geen generieke 'Archieven: …'-label.
$hero_title   = '';
$hero_eyebrow = '';
$hero_crumb   = '';
if ( is_post_type_archive( 'team' ) ) {
	$hero_title   = __( 'Teams', 'eboh-v2' );
	$hero_eyebrow = __( 'Onze teams', 'eboh-v2' );
	$hero_crumb   = __( 'Teams', 'eboh-v2' );
} else {
	$default_title = wp_strip_all_tags( get_the_archive_title() );
	$hero_title    = preg_replace( '/^(Archieven:|Archive:)\s*/i', '', $default_title );
	$hero_eyebrow  = __( 'Overzicht', 'eboh-v2' );
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
		<?php if ( have_posts() ) : ?>

			<?php if ( is_post_type_archive( 'team' ) ) : ?>

				<?php
				// Verzamel alle teams; isoleer EBOH 1 voor full-width feature.
				$all_teams = array();
				while ( have_posts() ) :
					the_post();
					$all_teams[] = get_post();
				endwhile;
				wp_reset_postdata();

				$placeholder = get_template_directory_uri() . '/assets/images/team-placeholder.jpg';

				$eboh1 = null;
				$rest  = array();
				foreach ( $all_teams as $t ) {
					if ( strcasecmp( trim( $t->post_title ), 'EBOH 1' ) === 0 ) {
						$eboh1 = $t;
					} else {
						$rest[] = $t;
					}
				}
				?>

				<?php if ( $eboh1 ) :
					$feat_url = get_the_post_thumbnail_url( $eboh1->ID, 'large' ) ?: $placeholder;
					?>
					<a class="team-feature" href="<?php echo esc_url( get_permalink( $eboh1->ID ) ); ?>">
						<div class="team-feature__media" style="background-image: url('<?php echo esc_url( $feat_url ); ?>');" role="img" aria-label="<?php echo esc_attr( $eboh1->post_title ); ?>"></div>
						<div class="team-feature__body">
							<h2 class="team-feature__name"><?php echo esc_html( $eboh1->post_title ); ?></h2>
							<?php $klasse = get_post_meta( $eboh1->ID, 'klasse', true ); ?>
							<?php if ( $klasse ) : ?>
								<p class="team-feature__meta"><?php echo esc_html( $klasse ); ?></p>
							<?php endif; ?>
						</div>
					</a>
				<?php endif; ?>

				<div class="teams-overview-grid">
					<?php foreach ( $rest as $t ) :
						$thumb = get_the_post_thumbnail_url( $t->ID, 'large' ) ?: $placeholder;
						?>
						<a class="team-tile" href="<?php echo esc_url( get_permalink( $t->ID ) ); ?>">
							<div class="team-tile__media" style="background-image: url('<?php echo esc_url( $thumb ); ?>');" role="img" aria-label="<?php echo esc_attr( $t->post_title ); ?>"></div>
							<div class="team-tile__body">
								<h3 class="team-tile__name"><?php echo esc_html( $t->post_title ); ?></h3>
							</div>
						</a>
					<?php endforeach; ?>
				</div>

			<?php else : ?>
				<div class="news-grid">
					<?php while ( have_posts() ) : the_post(); ?>
						<?php get_template_part( 'parts/news-card' ); ?>
					<?php endwhile; ?>
				</div>
				<div style="margin-top:48px;text-align:center;">
					<?php
					the_posts_pagination( array(
						'prev_text' => esc_html__( '← Vorige', 'eboh-v2' ),
						'next_text' => esc_html__( 'Volgende →', 'eboh-v2' ),
					) );
					?>
				</div>
			<?php endif; ?>

		<?php else : ?>
			<p><?php esc_html_e( 'Geen items gevonden.', 'eboh-v2' ); ?></p>
		<?php endif; ?>
	</div>
</main>

<?php get_footer();
