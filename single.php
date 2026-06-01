<?php
/**
 * Single Post / Team Template for EBOH
 *
 * @package EBOH
 * @since 2.0.0
 */

get_header();

while ( have_posts() ) :
	the_post();

	if ( get_post_type() === 'team' ) :

		// -- Team Template --
		// Featured image van het team-post heeft voorrang; anders altijd de
		// team-placeholder zodat de hero nooit zwart blijft.
		$team_placeholder = get_template_directory_uri() . '/assets/images/team-placeholder.jpg';
		$thumb_id         = get_post_thumbnail_id();
		$thumb_url        = $thumb_id ? get_the_post_thumbnail_url( null, 'full' ) : $team_placeholder;
		if ( empty( $thumb_url ) ) { $thumb_url = $team_placeholder; }
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
					<a href="<?php echo esc_url( home_url( '/teams' ) ); ?>"><?php esc_html_e( 'Teams', 'eboh' ); ?></a> /
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

				<?php
				// Programma & Uitslagen via Sportlink API
				$team_name   = get_the_title();
				$programma   = function_exists( 'eboh_get_team_programma' ) ? eboh_get_team_programma( $team_name, 5 ) : array();
				$uitslagen   = function_exists( 'eboh_get_team_uitslagen' ) ? eboh_get_team_uitslagen( $team_name, 5 ) : array();
				?>

				<?php if ( ! empty( $programma ) || ! empty( $uitslagen ) ) : ?>
				<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:32px;margin-bottom:48px;">

					<?php if ( ! empty( $programma ) ) : ?>
					<div>
						<h2 class="page-section__title page-section__title--bar"><?php esc_html_e( 'Programma', 'eboh' ); ?></h2>
						<table style="width:100%;border-collapse:collapse;font-family:'Work Sans',sans-serif;font-size:14px;">
							<thead>
								<tr style="border-bottom:2px solid var(--primary-red,#E80808);text-align:left;">
									<th style="padding:8px 4px;font-family:'Oswald',sans-serif;text-transform:uppercase;font-size:12px;letter-spacing:0.08em;">Datum</th>
									<th style="padding:8px 4px;font-family:'Oswald',sans-serif;text-transform:uppercase;font-size:12px;letter-spacing:0.08em;">Wedstrijd</th>
									<th style="padding:8px 4px;font-family:'Oswald',sans-serif;text-transform:uppercase;font-size:12px;letter-spacing:0.08em;">Tijd</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $programma as $w ) : ?>
								<?php
									$datum = '';
									foreach ( array( 'wedstrijddatum', 'datum', 'wedstrijddatumtijd' ) as $f ) {
										if ( ! empty( $w[ $f ] ) ) { $datum = $w[ $f ]; break; }
									}
									$ts = $datum ? strtotime( $datum ) : 0;
									$datum_fmt = $ts ? date_i18n( 'D j M', $ts ) : $datum;
									$thuis = isset( $w['thuisteam'] ) ? $w['thuisteam'] : '';
									$uit   = isset( $w['uitteam'] )  ? $w['uitteam']  : '';
									$tijd  = isset( $w['aanvangstijd'] ) ? $w['aanvangstijd'] : '';
								?>
								<tr style="border-bottom:1px solid rgba(0,0,0,0.08);">
									<td style="padding:10px 4px;white-space:nowrap;"><?php echo esc_html( $datum_fmt ); ?></td>
									<td style="padding:10px 4px;"><strong><?php echo esc_html( $thuis ); ?></strong> - <?php echo esc_html( $uit ); ?></td>
									<td style="padding:10px 4px;white-space:nowrap;"><?php echo esc_html( $tijd ); ?></td>
								</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
					<?php endif; ?>

					<?php if ( ! empty( $uitslagen ) ) : ?>
					<div>
						<h2 class="page-section__title page-section__title--bar"><?php esc_html_e( 'Uitslagen', 'eboh' ); ?></h2>
						<table style="width:100%;border-collapse:collapse;font-family:'Work Sans',sans-serif;font-size:14px;">
							<thead>
								<tr style="border-bottom:2px solid var(--primary-red,#E80808);text-align:left;">
									<th style="padding:8px 4px;font-family:'Oswald',sans-serif;text-transform:uppercase;font-size:12px;letter-spacing:0.08em;">Datum</th>
									<th style="padding:8px 4px;font-family:'Oswald',sans-serif;text-transform:uppercase;font-size:12px;letter-spacing:0.08em;">Wedstrijd</th>
									<th style="padding:8px 4px;font-family:'Oswald',sans-serif;text-transform:uppercase;font-size:12px;letter-spacing:0.08em;">Uitslag</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $uitslagen as $w ) : ?>
								<?php
									$datum = '';
									foreach ( array( 'wedstrijddatum', 'datum' ) as $f ) {
										if ( ! empty( $w[ $f ] ) ) { $datum = $w[ $f ]; break; }
									}
									$ts = $datum ? strtotime( $datum ) : 0;
									$datum_fmt = $ts ? date_i18n( 'D j M', $ts ) : $datum;
									$thuis = isset( $w['thuisteam'] ) ? $w['thuisteam'] : '';
									$uit   = isset( $w['uitteam'] )  ? $w['uitteam']  : '';
									$score = isset( $w['uitslag'] )  ? $w['uitslag']  : '';
								?>
								<tr style="border-bottom:1px solid rgba(0,0,0,0.08);">
									<td style="padding:10px 4px;white-space:nowrap;"><?php echo esc_html( $datum_fmt ); ?></td>
									<td style="padding:10px 4px;"><strong><?php echo esc_html( $thuis ); ?></strong> - <?php echo esc_html( $uit ); ?></td>
									<td style="padding:10px 4px;font-weight:600;white-space:nowrap;"><?php echo esc_html( $score ); ?></td>
								</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
					<?php endif; ?>

				</div>
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

	else :

		// -- News / Post Template --
		$thumb_url = get_the_post_thumbnail_url( null, 'full' );
		$category  = get_the_category();
		$cat_name  = ! empty( $category ) ? $category[0]->name : '';
		?>

		<section class="page-hero">
			<div class="page-hero__container">
				<p class="page-hero__breadcrumbs">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'eboh' ); ?></a> /
					<a href="<?php echo esc_url( get_post_type_archive_link( 'post' ) ?: home_url( '/nieuws' ) ); ?>"><?php esc_html_e( 'Nieuws', 'eboh' ); ?></a> /
					<?php esc_html_e( 'Artikel', 'eboh' ); ?>
				</p>
				<div class="article-meta" style="margin-bottom:16px;">
					<?php if ( $cat_name ) : ?>
						<span class="article-meta__tag"><?php echo esc_html( $cat_name ); ?></span>
					<?php endif; ?>
					<span><?php echo esc_html( date_i18n( 'j M Y', strtotime( get_the_date() ) ) ); ?></span>
					<span>&middot; <?php printf( esc_html__( 'Door %s', 'eboh' ), esc_html( get_the_author() ) ); ?></span>
				</div>
				<h1 class="page-hero__title"><?php the_title(); ?></h1>
				<?php if ( has_excerpt() ) : ?>
					<p class="page-hero__subtitle"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>
			</div>
		</section>

		<main id="main" class="page-shell">
			<div class="page-container page-container--narrow">
				<?php if ( $thumb_url ) : ?>
					<div class="article-hero" style="background-image: url('<?php echo esc_url( $thumb_url ); ?>');"></div>
				<?php endif; ?>

				<article class="article-body">
					<?php
					the_content();
					wp_link_pages( array(
						'before' => '<div class="page-links">' . esc_html__( 'Pagina:', 'eboh' ),
						'after'  => '</div>',
					) );
					?>
				</article>

				<div style="margin-top:48px;padding-top:32px;border-top:1px solid rgba(0,0,0,0.08);display:flex;gap:14px;flex-wrap:wrap;align-items:center;">
					<span style="font-family:'Oswald',sans-serif;text-transform:uppercase;letter-spacing:0.08em;font-size:13px;"><?php esc_html_e( 'Delen:', 'eboh' ); ?></span>
					<a style="display:inline-block;padding:8px 16px;background:var(--dark-section,#343B41);color:#fff;border-radius:4px;text-decoration:none;font-family:'Oswald',sans-serif;font-size:13px;text-transform:uppercase;letter-spacing:0.05em;" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo esc_url( get_the_permalink() ); ?>" target="_blank" rel="noopener">Facebook</a>
					<a style="display:inline-block;padding:8px 16px;background:var(--dark-section,#343B41);color:#fff;border-radius:4px;text-decoration:none;font-family:'Oswald',sans-serif;font-size:13px;text-transform:uppercase;letter-spacing:0.05em;" href="https://twitter.com/intent/tweet?url=<?php echo esc_url( get_the_permalink() ); ?>&text=<?php echo esc_attr( get_the_title() ); ?>" target="_blank" rel="noopener">X</a>
					<a style="display:inline-block;padding:8px 16px;background:var(--dark-section,#343B41);color:#fff;border-radius:4px;text-decoration:none;font-family:'Oswald',sans-serif;font-size:13px;text-transform:uppercase;letter-spacing:0.05em;" href="https://wa.me/?text=<?php echo esc_attr( get_the_title() ); ?>%20<?php echo esc_url( get_the_permalink() ); ?>" target="_blank" rel="noopener">WhatsApp</a>
				</div>
			</div>
		</main>

		<?php
		// Related articles
		$cat_ids = array();
		foreach ( $category as $cat ) { $cat_ids[] = $cat->term_id; }
		$related = new WP_Query( array(
			'post__not_in'   => array( get_the_ID() ),
			'posts_per_page' => 3,
			'category__in'   => $cat_ids,
			'orderby'        => 'rand',
		) );
		if ( $related->have_posts() ) :
			?>
			<section class="page-shell page-shell--muted">
				<div class="page-container">
					<h2 class="page-section__title page-section__title--bar"><?php esc_html_e( 'Gerelateerd nieuws', 'eboh' ); ?></h2>
					<div class="news-grid" style="grid-template-columns:repeat(auto-fill,minmax(280px,1fr));">
						<?php
						while ( $related->have_posts() ) {
							$related->the_post();
							get_template_part( 'parts/news-card' );
						}
						wp_reset_postdata();
						?>
					</div>
				</div>
			</section>
			<?php
		endif;


	endif;

endwhile;

get_footer();
