<?php
/**
 * Single Team Template for EBOH
 *
 * @package EBOH
 * @since 2.0.0
 */

get_header();

while ( have_posts() ) :
	the_post();

	$thumb_url  = get_the_post_thumbnail_url( null, 'full' );
	$klasse     = get_post_meta( get_the_ID(), 'klasse', true );
	$regio      = get_post_meta( get_the_ID(), 'regio', true );
	$trainer    = get_post_meta( get_the_ID(), 'trainer', true );
	$categories = get_the_terms( get_the_ID(), 'team_category' );
	$cat_names  = array();
	if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
		foreach ( $categories as $cat ) {
			$cat_names[] = $cat->name;
		}
	}
	?>

	<section class="page-hero<?php echo $thumb_url ? ' page-hero--image' : ''; ?>"
		<?php if ( $thumb_url ) : ?>style="background-image: url('<?php echo esc_url( $thumb_url ); ?>');"<?php endif; ?>>
		<div class="page-hero__container">
			<p class="page-hero__breadcrumbs">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'eboh' ); ?></a> /
				<a href="<?php echo esc_url( get_post_type_archive_link( 'team' ) ); ?>"><?php esc_html_e( 'Teams', 'eboh' ); ?></a> /
				<?php the_title(); ?>
			</p>
			<?php if ( ! empty( $cat_names ) ) : ?>
				<span class="page-hero__eyebrow"><?php echo esc_html( implode( ', ', $cat_names ) ); ?></span>
			<?php endif; ?>
			<h1 class="page-hero__title"><?php the_title(); ?></h1>
			<?php if ( $klasse ) : ?>
				<p class="page-hero__subtitle"><?php echo esc_html( $klasse ); ?><?php if ( $regio ) : ?> &mdash; <?php echo esc_html( $regio ); ?><?php endif; ?></p>
			<?php endif; ?>
		</div>
	</section>

	<main id="main" class="page-shell">
		<div class="page-container">

			<!-- Team Info Block -->
			<div class="team-info" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:24px;margin-bottom:48px;padding:32px;background:var(--dark-section,#343B41);border-radius:8px;color:#fff;">
				<?php if ( $klasse ) : ?>
				<div class="team-info__item">
					<span class="team-info__label" style="display:block;font-family:'Oswald',sans-serif;text-transform:uppercase;letter-spacing:0.08em;font-size:12px;color:rgba(255,255,255,0.6);margin-bottom:4px;"><?php esc_html_e( 'Klasse', 'eboh' ); ?></span>
					<span class="team-info__value" style="font-family:'Oswald',sans-serif;font-size:20px;font-weight:600;"><?php echo esc_html( $klasse ); ?></span>
				</div>
				<?php endif; ?>
				<?php if ( $regio ) : ?>
				<div class="team-info__item">
					<span class="team-info__label" style="display:block;font-family:'Oswald',sans-serif;text-transform:uppercase;letter-spacing:0.08em;font-size:12px;color:rgba(255,255,255,0.6);margin-bottom:4px;"><?php esc_html_e( 'Regio', 'eboh' ); ?></span>
					<span class="team-info__value" style="font-family:'Oswald',sans-serif;font-size:20px;font-weight:600;"><?php echo esc_html( $regio ); ?></span>
				</div>
				<?php endif; ?>
				<?php if ( $trainer ) : ?>
				<div class="team-info__item">
					<span class="team-info__label" style="display:block;font-family:'Oswald',sans-serif;text-transform:uppercase;letter-spacing:0.08em;font-size:12px;color:rgba(255,255,255,0.6);margin-bottom:4px;"><?php esc_html_e( 'Trainer(s)', 'eboh' ); ?></span>
					<span class="team-info__value" style="font-family:'Oswald',sans-serif;font-size:20px;font-weight:600;"><?php echo esc_html( $trainer ); ?></span>
				</div>
				<?php endif; ?>
				<?php if ( ! empty( $cat_names ) ) : ?>
				<div class="team-info__item">
					<span class="team-info__label" style="display:block;font-family:'Oswald',sans-serif;text-transform:uppercase;letter-spacing:0.08em;font-size:12px;color:rgba(255,255,255,0.6);margin-bottom:4px;"><?php esc_html_e( 'Afdeling', 'eboh' ); ?></span>
					<span class="team-info__value" style="font-family:'Oswald',sans-serif;font-size:20px;font-weight:600;"><?php echo esc_html( implode( ', ', $cat_names ) ); ?></span>
				</div>
				<?php endif; ?>
			</div>

			<?php if ( $thumb_url ) : ?>
				<div class="article-hero" style="background-image: url('<?php echo esc_url( $thumb_url ); ?>');margin-bottom:32px;"></div>
			<?php endif; ?>

			<?php if ( get_the_content() ) : ?>
			<article class="article-body" style="margin-bottom:48px;">
				<?php the_content(); ?>
			</article>
			<?php endif; ?>

		</div>
	</main>

	<?php
	// Related teams from same category
	if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) :
		$cat_ids = wp_list_pluck( $categories, 'term_id' );
		$related = new WP_Query( array(
			'post_type'      => 'team',
			'post__not_in'   => array( get_the_ID() ),
			'posts_per_page' => 6,
			'tax_query'      => array( array(
				'taxonomy' => 'team_category',
				'field'    => 'term_id',
				'terms'    => $cat_ids,
			) ),
			'orderby' => 'title',
			'order'   => 'ASC',
		) );

		if ( $related->have_posts() ) :
			?>
			<section class="page-shell page-shell--muted">
				<div class="page-container">
					<h2 class="page-section__title page-section__title--bar"><?php esc_html_e( 'Andere teams', 'eboh' ); ?></h2>
					<div class="teams-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:18px;">
						<?php
						while ( $related->have_posts() ) : $related->the_post();
							$rel_thumb = get_the_post_thumbnail_url( get_the_ID(), 'full' ) ?: get_template_directory_uri() . '/assets/images/team-placeholder.jpg';
							?>
							<a href="<?php the_permalink(); ?>" class="team-card" style="background-image: url('<?php echo esc_url( $rel_thumb ); ?>');">
								<div class="team-card__content"><h3 class="team-card__name"><?php the_title(); ?></h3></div>
							</a>
						<?php endwhile; wp_reset_postdata(); ?>
					</div>
				</div>
			</section>
			<?php
		endif;
	endif;

endwhile;

get_footer();
