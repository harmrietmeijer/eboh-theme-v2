<?php
/**
 * Custom Shortcodes for EBOH Theme
 *
 * @package EBOH
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sponsor Grid Shortcode
 * Usage: [eboh_sponsor_grid]
 */
function eboh_sponsor_grid_shortcode( $atts ) {
	ob_start();
	?>
	<div class="sponsor-grid">
		<div class="sponsor-item">
			<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/sponsor-placeholder.png' ); ?>"
				 alt="<?php esc_attr_e( 'Sponsor', 'eboh' ); ?>">
		</div>
		<div class="sponsor-item">
			<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/sponsor-placeholder.png' ); ?>"
				 alt="<?php esc_attr_e( 'Sponsor', 'eboh' ); ?>">
		</div>
		<div class="sponsor-item">
			<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/sponsor-placeholder.png' ); ?>"
				 alt="<?php esc_attr_e( 'Sponsor', 'eboh' ); ?>">
		</div>
		<div class="sponsor-item">
			<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/sponsor-placeholder.png' ); ?>"
				 alt="<?php esc_attr_e( 'Sponsor', 'eboh' ); ?>">
		</div>
		<div class="sponsor-item">
			<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/sponsor-placeholder.png' ); ?>"
				 alt="<?php esc_attr_e( 'Sponsor', 'eboh' ); ?>">
		</div>
		<div class="sponsor-item">
			<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/sponsor-placeholder.png' ); ?>"
				 alt="<?php esc_attr_e( 'Sponsor', 'eboh' ); ?>">
		</div>
	</div>
	<?php
	return ob_get_clean();
}

add_shortcode( 'eboh_sponsor_grid', 'eboh_sponsor_grid_shortcode' );

/**
 * Team Grid Shortcode
 * Usage: [eboh_team_grid category="senioren" limit="6"]
 */
function eboh_team_grid_shortcode( $atts ) {
	$atts = shortcode_atts( array(
		'category' => '',
		'limit'    => 6,
	), $atts, 'eboh_team_grid' );

	$args = array(
		'post_type'      => 'team',
		'posts_per_page' => intval( $atts['limit'] ),
		'orderby'        => 'title',
		'order'          => 'ASC',
	);

	if ( ! empty( $atts['category'] ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'team_category',
				'field'    => 'slug',
				'terms'    => sanitize_text_field( $atts['category'] ),
			),
		);
	}

	$query = new WP_Query( $args );

	ob_start();
	?>
	<div class="team-grid">
		<?php
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$team_image = get_the_post_thumbnail_url( get_the_ID(), 'eboh-team' );
				if ( ! $team_image ) {
					$team_image = get_template_directory_uri() . '/assets/images/team-placeholder.jpg';
				}
				?>
				<a href="<?php the_permalink(); ?>" class="team-card" style="background-image: url('<?php echo esc_url( $team_image ); ?>');">
					<div class="team-card-image" style="background-image: url('<?php echo esc_url( $team_image ); ?>');"></div>
					<div class="team-card-overlay"></div>
					<div class="team-card-overlay">
						<div class="team-card-name">
							<?php the_title(); ?>
						</div>
						<div class="team-card-position">
							<?php
							$terms = get_the_terms( get_the_ID(), 'team_category' );
							if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
								echo esc_html( $terms[0]->name );
							}
							?>
						</div>
					</div>
				</a>
				<?php
			}
			wp_reset_postdata();
		}
		?>
	</div>
	<?php
	return ob_get_clean();
}

add_shortcode( 'eboh_team_grid', 'eboh_team_grid_shortcode' );

/**
 * Match Ticker Shortcode
 * Usage: [eboh_match_ticker]
 */
function eboh_match_ticker_shortcode( $atts ) {
	ob_start();
	?>
	<div class="match-ticker">
		<div class="match-ticker-info">
			<div class="match-ticker-team">
				<span><?php esc_html_e( 'EBOH', 'eboh' ); ?></span>
			</div>
			<div class="match-ticker-vs">
				<?php esc_html_e( 'vs', 'eboh' ); ?>
			</div>
			<div class="match-ticker-team">
				<span><?php esc_html_e( 'SC Cambuur', 'eboh' ); ?></span>
			</div>
			<div class="match-ticker-countdown">
				<?php esc_html_e( 'Zaterdag 19 maart | 15:00 uur', 'eboh' ); ?>
			</div>
		</div>
		<div class="match-ticker-action">
			<span><?php esc_html_e( 'Meer info', 'eboh' ); ?></span>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

add_shortcode( 'eboh_match_ticker', 'eboh_match_ticker_shortcode' );

/**
 * News Grid Shortcode
 * Usage: [eboh_news_grid category="club-nieuws" limit="3"]
 */
function eboh_news_grid_shortcode( $atts ) {
	$atts = shortcode_atts( array(
		'category' => '',
		'limit'    => 3,
	), $atts, 'eboh_news_grid' );

	$args = array(
		'post_type'      => 'post',
		'posts_per_page' => intval( $atts['limit'] ),
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	if ( ! empty( $atts['category'] ) ) {
		$args['category_name'] = sanitize_text_field( $atts['category'] );
	}

	$query = new WP_Query( $args );

	ob_start();
	?>
	<div class="news-grid">
		<?php
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$categories = get_the_category();
				?>
				<article class="news-card">
					<div class="news-card-image">
						<?php
						if ( has_post_thumbnail() ) {
							the_post_thumbnail( 'eboh-card' );
						}
						?>
						<span class="news-card-tag">
							<?php
							if ( ! empty( $categories ) ) {
								echo esc_html( $categories[0]->name );
							}
							?>
						</span>
					</div>
					<div class="news-card-content">
						<div class="news-card-meta">
							<?php echo esc_html( date_i18n( 'j M Y', strtotime( get_the_date() ) ) ); ?>
						</div>
						<h3 class="news-card-title">
							<?php the_title(); ?>
						</h3>
						<p class="news-card-excerpt">
							<?php echo esc_html( wp_trim_words( get_the_excerpt(), 15 ) ); ?>
						</p>
						<a href="<?php the_permalink(); ?>" class="news-card-link">
							<?php esc_html_e( 'Lees verder →', 'eboh' ); ?>
						</a>
					</div>
				</article>
				<?php
			}
			wp_reset_postdata();
		}
		?>
	</div>
	<?php
	return ob_get_clean();
}

add_shortcode( 'eboh_news_grid', 'eboh_news_grid_shortcode' );
