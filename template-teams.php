<?php
/**
 * Template Name: Teams
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
		<span class="page-hero__eyebrow"><?php esc_html_e( 'Onze teams', 'eboh' ); ?></span>
		<h1 class="page-hero__title"><?php the_title(); ?></h1>
		<?php if ( get_the_excerpt() ) : ?>
			<p class="page-hero__subtitle"><?php echo esc_html( get_the_excerpt() ); ?></p>
		<?php endif; ?>
	</div>
</section>

<main id="main" class="page-shell page-shell--muted">
	<div class="page-container">
		<?php if ( get_the_content() ) : ?>
			<div class="page-content" style="margin-bottom:48px;"><?php the_content(); ?></div>
		<?php endif; ?>

		<?php
		$categories = get_terms( array( 'taxonomy' => 'team_category', 'hide_empty' => true ) );
		if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) :
			foreach ( $categories as $cat ) :
				$teams = new WP_Query( array(
					'post_type'      => 'team',
					'posts_per_page' => -1,
					'tax_query'      => array( array(
						'taxonomy' => 'team_category',
						'field'    => 'term_id',
						'terms'    => $cat->term_id,
					) ),
					'orderby' => 'title',
					'order'   => 'ASC',
				) );
				if ( $teams->have_posts() ) :
					?>
					<div class="page-section">
						<h2 class="page-section__title page-section__title--bar"><?php echo esc_html( $cat->name ); ?></h2>
						<div class="teams-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:18px;">
							<?php while ( $teams->have_posts() ) : $teams->the_post();
								$thumb = get_the_post_thumbnail_url( get_the_ID(), 'full' ) ?: get_template_directory_uri() . '/assets/images/team-placeholder.jpg';
								?>
								<a href="<?php the_permalink(); ?>" class="team-card" style="background-image: url('<?php echo esc_url( $thumb ); ?>');">
									<div class="team-card__content"><h3 class="team-card__name"><?php the_title(); ?></h3></div>
								</a>
							<?php endwhile; wp_reset_postdata(); ?>
						</div>
					</div>
					<?php
				endif;
			endforeach;
		else :
			// No taxonomy / fallback: list all teams
			$teams = new WP_Query( array( 'post_type' => 'team', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC' ) );
			?>
			<div class="teams-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:18px;">
				<?php
				if ( $teams->have_posts() ) :
					while ( $teams->have_posts() ) : $teams->the_post();
						$thumb = get_the_post_thumbnail_url( get_the_ID(), 'full' ) ?: get_template_directory_uri() . '/assets/images/team-placeholder.jpg';
						?>
						<a href="<?php the_permalink(); ?>" class="team-card" style="background-image: url('<?php echo esc_url( $thumb ); ?>');">
							<div class="team-card__content"><h3 class="team-card__name"><?php the_title(); ?></h3></div>
						</a>
					<?php endwhile; wp_reset_postdata();
				else :
					$placeholders = array( 'EBOH 1', 'EBOH 2', 'JO19-1', 'JO15-1', 'Dames 1', 'Veteranen' );
					foreach ( $placeholders as $name ) {
						$img = get_template_directory_uri() . '/assets/images/team-placeholder.jpg';
						echo '<a href="#" class="team-card" style="background-image: url(' . esc_url( $img ) . ');"><div class="team-card__content"><h3 class="team-card__name">' . esc_html( $name ) . '</h3></div></a>';
					}
				endif;
				?>
			</div>
		<?php endif; ?>
	</div>
</main>

<?php get_footer();
