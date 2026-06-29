<?php
/**
 * Single Team Template — EBOH v2
 *
 * Layout (klant-wens 29-06-2026):
 *  1. Page-hero (geen teamfoto-achtergrond, alleen naam/niveau/categorie/breadcrumbs)
 *  2. Sportlink-widget: volgende wedstrijd | uitslag | volledige stand
 *  3. Spelers/staf-grid: eerst staf, dan spelers; foto + naam onder de foto;
 *     placeholder bij missende foto.
 *
 * @package EBOH
 * @since 3.0.0
 */

get_header();

while ( have_posts() ) :
	the_post();

	$team_name  = get_the_title();
	$klasse     = get_post_meta( get_the_ID(), 'klasse', true );
	$regio      = get_post_meta( get_the_ID(), 'regio', true );
	$trainer    = get_post_meta( get_the_ID(), 'trainer', true );
	$categories = get_the_terms( get_the_ID(), 'team_category' );
	$cat_names  = array();
	if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
		foreach ( $categories as $cat ) { $cat_names[] = $cat->name; }
	}

	// Sportlink-data
	$next_match  = function_exists( 'eboh_get_next_match' )        ? eboh_get_next_match( $team_name ) : null;
	$last_result = function_exists( 'eboh_get_last_result' )       ? eboh_get_last_result( $team_name ) : null;
	$full_stand  = function_exists( 'eboh_get_stand_around_team' ) ? eboh_get_stand_around_team( $team_name, 99 ) : array();

	// Spelers & staf — via post meta (ACF/CMB2 of native), of fallback: parse
	// uit een eenvoudige textarea-meta 'eboh_team_roster' met formaat
	// "Naam|Rol|attachment_id\nNaam|Rol|attachment_id". Eerst alle staf-rollen,
	// dan spelers.
	$roster_raw = get_post_meta( get_the_ID(), 'eboh_team_roster', true );
	$staff = array();
	$players = array();
	if ( ! empty( $roster_raw ) ) {
		$lines = preg_split( '/\r?\n/', trim( $roster_raw ) );
		foreach ( $lines as $line ) {
			$parts = array_map( 'trim', explode( '|', $line ) );
			$name = $parts[0] ?? '';
			$role = $parts[1] ?? '';
			$att  = isset( $parts[2] ) ? intval( $parts[2] ) : 0;
			if ( empty( $name ) ) { continue; }
			$photo = $att ? wp_get_attachment_image_url( $att, 'medium' ) : '';
			$item = array( 'name' => $name, 'role' => $role, 'photo' => $photo );
			// Staf-rollen — heuristiek
			if ( $role && preg_match( '/(trainer|coach|leider|verzorg|staf|manager|assistent|keepers)/i', $role ) ) {
				$staff[] = $item;
			} else {
				$players[] = $item;
			}
		}
	}
	$placeholder_person = get_template_directory_uri() . '/assets/images/team-placeholder.jpg';
	?>

	<section class="page-hero">
		<div class="page-hero__container">
			<p class="page-hero__breadcrumbs">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'eboh-v2' ); ?></a> /
				<a href="<?php echo esc_url( get_post_type_archive_link( 'team' ) ); ?>"><?php esc_html_e( 'Teams', 'eboh-v2' ); ?></a> /
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

			<?php if ( $trainer ) : ?>
				<p class="team-trainer"><strong><?php esc_html_e( 'Trainer(s):', 'eboh-v2' ); ?></strong> <?php echo esc_html( $trainer ); ?></p>
			<?php endif; ?>

			<!-- Sportlink widget per team -->
			<section class="match-widget" style="padding-top:0;">
				<div class="match-widget__container">
					<div class="match-widget__grid">

						<article class="mw-card mw-card--next">
							<div class="mw-card__competition">
								<span class="mw-card__competition-name"><?php echo esc_html( $next_match['competitie'] ?? __( 'Volgende wedstrijd', 'eboh-v2' ) ); ?></span>
							</div>
							<?php if ( $next_match ) : ?>
								<div class="mw-card__match">
									<div class="mw-card__team mw-card__team--home">
										<div class="mw-card__crest" aria-hidden="true"><?php echo esc_html( mb_substr( $next_match['thuisteam'], 0, 1 ) ); ?></div>
										<div class="mw-card__team-name"><?php echo esc_html( $next_match['thuisteam'] ); ?></div>
									</div>
									<div class="mw-card__center">
										<div class="mw-card__date"><?php echo esc_html( $next_match['datum_kort'] ); ?></div>
										<div class="mw-card__time"><?php echo esc_html( $next_match['tijd'] ); ?></div>
									</div>
									<div class="mw-card__team mw-card__team--away">
										<div class="mw-card__crest" aria-hidden="true"><?php echo esc_html( mb_substr( $next_match['uitteam'], 0, 1 ) ); ?></div>
										<div class="mw-card__team-name"><?php echo esc_html( $next_match['uitteam'] ); ?></div>
									</div>
								</div>
							<?php else : ?>
								<div class="mw-card__empty"><?php esc_html_e( 'Geen wedstrijd gepland', 'eboh-v2' ); ?></div>
							<?php endif; ?>
						</article>

						<article class="mw-card mw-card--result">
							<div class="mw-card__competition">
								<span class="mw-card__competition-name"><?php esc_html_e( 'Laatste uitslag', 'eboh-v2' ); ?></span>
							</div>
							<?php if ( $last_result ) : ?>
								<div class="mw-card__match">
									<div class="mw-card__team mw-card__team--home">
										<div class="mw-card__crest" aria-hidden="true"><?php echo esc_html( mb_substr( $last_result['thuisteam'], 0, 1 ) ); ?></div>
										<div class="mw-card__team-name"><?php echo esc_html( $last_result['thuisteam'] ); ?></div>
									</div>
									<div class="mw-card__center mw-card__center--score">
										<div class="mw-card__score">
											<span><?php echo esc_html( $last_result['score_thuis'] ?? '-' ); ?></span>
											<span class="mw-card__score-dash">–</span>
											<span><?php echo esc_html( $last_result['score_uit'] ?? '-' ); ?></span>
										</div>
										<div class="mw-card__date mw-card__date--past"><?php echo esc_html( $last_result['datum_kort'] ); ?></div>
									</div>
									<div class="mw-card__team mw-card__team--away">
										<div class="mw-card__crest" aria-hidden="true"><?php echo esc_html( mb_substr( $last_result['uitteam'], 0, 1 ) ); ?></div>
										<div class="mw-card__team-name"><?php echo esc_html( $last_result['uitteam'] ); ?></div>
									</div>
								</div>
							<?php else : ?>
								<div class="mw-card__empty"><?php esc_html_e( 'Nog geen uitslagen', 'eboh-v2' ); ?></div>
							<?php endif; ?>
						</article>

						<article class="mw-card mw-card--stand mw-card--stand-full">
							<div class="mw-card__competition">
								<span class="mw-card__competition-name"><?php esc_html_e( 'Volledige stand', 'eboh-v2' ); ?></span>
							</div>
							<?php if ( ! empty( $full_stand ) ) : ?>
								<table class="mw-stand mw-stand--full">
									<thead>
										<tr>
											<th class="mw-stand__pos">#</th>
											<th class="mw-stand__team"><?php esc_html_e( 'Team', 'eboh-v2' ); ?></th>
											<th>GS</th>
											<th>W</th>
											<th>G</th>
											<th>V</th>
											<th>Pnt</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ( $full_stand as $row ) :
											$naam     = isset( $row['teamnaam'] ) ? $row['teamnaam'] : ( isset( $row['naam'] ) ? $row['naam'] : '' );
											$gespeeld = isset( $row['gespeeldewedstrijden'] ) ? $row['gespeeldewedstrijden'] : '0';
											$gewonnen = isset( $row['gewonnen'] ) ? $row['gewonnen'] : 0;
											$gelijk   = isset( $row['gelijk'] ) ? $row['gelijk'] : 0;
											$verloren = isset( $row['verloren'] ) ? $row['verloren'] : 0;
											$punten   = isset( $row['totaalpunten'] ) ? $row['totaalpunten'] : ( isset( $row['punten'] ) ? $row['punten'] : '0' );
											$positie  = isset( $row['positie'] ) ? $row['positie'] : '';
											$is_target = ! empty( $row['_is_target'] );
											?>
											<tr<?php echo $is_target ? ' class="is-target"' : ''; ?>>
												<td class="mw-stand__pos"><?php echo esc_html( $positie ); ?></td>
												<td class="mw-stand__team"><?php echo esc_html( $naam ); ?></td>
												<td><?php echo esc_html( $gespeeld ); ?></td>
												<td><?php echo esc_html( $gewonnen ); ?></td>
												<td><?php echo esc_html( $gelijk ); ?></td>
												<td><?php echo esc_html( $verloren ); ?></td>
												<td class="mw-stand__pts"><?php echo esc_html( $punten ); ?></td>
											</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							<?php else : ?>
								<div class="mw-card__empty"><?php esc_html_e( 'Stand nog niet beschikbaar', 'eboh-v2' ); ?></div>
							<?php endif; ?>
						</article>

					</div>
				</div>
			</section>

			<!-- Optionele content uit WP-editor -->
			<?php if ( get_the_content() ) : ?>
				<article class="article-body" style="margin:48px 0;">
					<?php the_content(); ?>
				</article>
			<?php endif; ?>

			<!-- Staf-grid -->
			<?php if ( ! empty( $staff ) ) : ?>
				<section class="roster">
					<h2 class="roster__title"><?php esc_html_e( 'Staf', 'eboh-v2' ); ?></h2>
					<div class="roster-grid">
						<?php foreach ( $staff as $p ) : ?>
							<div class="roster-card">
								<div class="roster-card__photo" style="background-image: url('<?php echo esc_url( $p['photo'] ?: $placeholder_person ); ?>');"></div>
								<div class="roster-card__body">
									<h3 class="roster-card__name"><?php echo esc_html( $p['name'] ); ?></h3>
									<?php if ( $p['role'] ) : ?>
										<p class="roster-card__role"><?php echo esc_html( $p['role'] ); ?></p>
									<?php endif; ?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</section>
			<?php endif; ?>

			<!-- Spelers-grid -->
			<?php if ( ! empty( $players ) ) : ?>
				<section class="roster">
					<h2 class="roster__title"><?php esc_html_e( 'Spelers', 'eboh-v2' ); ?></h2>
					<div class="roster-grid">
						<?php foreach ( $players as $p ) : ?>
							<div class="roster-card">
								<div class="roster-card__photo" style="background-image: url('<?php echo esc_url( $p['photo'] ?: $placeholder_person ); ?>');"></div>
								<div class="roster-card__body">
									<h3 class="roster-card__name"><?php echo esc_html( $p['name'] ); ?></h3>
									<?php if ( $p['role'] ) : ?>
										<p class="roster-card__role"><?php echo esc_html( $p['role'] ); ?></p>
									<?php endif; ?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</section>
			<?php endif; ?>

			<?php if ( empty( $staff ) && empty( $players ) ) : ?>
				<p class="roster-empty" style="margin-top:48px;color:rgba(255,255,255,0.6);">
					<?php esc_html_e( 'Spelers en staf nog niet ingevoerd. Voeg in WP-admin een meta-veld', 'eboh-v2' ); ?>
					<code>eboh_team_roster</code>
					<?php esc_html_e( 'toe met regels in het formaat', 'eboh-v2' ); ?>
					<code>Naam|Rol|attachment_id</code> (één per regel).
				</p>
			<?php endif; ?>

		</div>
	</main>

<?php endwhile; ?>

<?php get_footer();
