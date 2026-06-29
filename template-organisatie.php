<?php
/**
 * Template Name: Organisatie
 * Template Post Type: page
 *
 * Tabs voor 12 organen (Bestuur, TC Senioren, etc). Per tab:
 *  - Naam orgaan + korte beschrijving
 *  - Leden-grid met foto's (placeholder bij missende foto), naam onder.
 *
 * Configuratie loopt via WP-admin → Customizer → EBOH Organisatie. Per orgaan
 * één textarea-veld 'eboh_org_<slug>_members' met regels in formaat:
 *
 *     Naam|Functie|attachment_id
 *
 * Optioneel een 'eboh_org_<slug>_intro' voor de korte tekst boven het grid.
 *
 * @package EBOH
 * @since 3.0.0
 */

get_header();

// Lijst van organen — zelfde slugs als in Customizer-velden.
$organen = array(
	'bestuur'                 => __( 'Bestuur', 'eboh-v2' ),
	'tc-senioren'             => __( 'Technische Commissie Senioren', 'eboh-v2' ),
	'tc-jeugd'                => __( 'Technische Commissie Jeugd', 'eboh-v2' ),
	'wedstrijdsecretariaat'   => __( 'Wedstrijdsecretariaat', 'eboh-v2' ),
	'media-communicatie'      => __( 'Media en Communicatie', 'eboh-v2' ),
	'sponsorcommissie'        => __( 'Sponsorcommissie', 'eboh-v2' ),
	'club-van-100'            => __( 'Club van 100', 'eboh-v2' ),
	'ledenadministratie'      => __( 'Ledenadministratie', 'eboh-v2' ),
	'contributieadministratie'=> __( 'Contributieadministratie', 'eboh-v2' ),
	'kascommissie'            => __( 'Kascommissie', 'eboh-v2' ),
	'activiteitencommissie'   => __( 'Activiteitencommissie', 'eboh-v2' ),
	'vertrouwenspersonen'     => __( 'Vertrouwenspersonen', 'eboh-v2' ),
);

$placeholder_person = get_template_directory_uri() . '/assets/images/team-placeholder.jpg';
$active_tab = isset( $_GET['orgaan'] ) ? sanitize_key( wp_unslash( $_GET['orgaan'] ) ) : 'bestuur';
if ( ! isset( $organen[ $active_tab ] ) ) {
	$active_tab = 'bestuur';
}

while ( have_posts() ) :
	the_post();
	?>

	<section class="page-hero">
		<div class="page-hero__container">
			<p class="page-hero__breadcrumbs"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'eboh-v2' ); ?></a> / <?php the_title(); ?></p>
			<span class="page-hero__eyebrow"><?php esc_html_e( 'De club', 'eboh-v2' ); ?></span>
			<h1 class="page-hero__title"><?php the_title(); ?></h1>
		</div>
	</section>

	<main id="main" class="page-shell">
		<div class="page-container">

			<!-- Page-content boven de tabs (optioneel) -->
			<?php if ( get_the_content() ) : ?>
				<div class="page-content" style="margin-bottom:32px;"><?php the_content(); ?></div>
			<?php endif; ?>

			<!-- Tab-navigatie -->
			<nav class="org-tabs" aria-label="<?php esc_attr_e( 'Organisatie', 'eboh-v2' ); ?>">
				<?php foreach ( $organen as $slug => $label ) :
					$href = esc_url( add_query_arg( 'orgaan', $slug, get_permalink() ) ) . '#org-content';
					$is_active = ( $active_tab === $slug ) ? ' is-active' : '';
					?>
					<a class="org-tabs__item<?php echo $is_active; ?>" href="<?php echo $href; ?>"><?php echo esc_html( $label ); ?></a>
				<?php endforeach; ?>
			</nav>

			<!-- Tab-content -->
			<section class="org-content" id="org-content">
				<?php
				$intro   = get_theme_mod( 'eboh_org_' . $active_tab . '_intro', '' );
				$members = get_theme_mod( 'eboh_org_' . $active_tab . '_members', '' );
				$leden   = array();
				if ( ! empty( $members ) ) {
					foreach ( preg_split( '/\r?\n/', trim( $members ) ) as $line ) {
						$parts = array_map( 'trim', explode( '|', $line ) );
						$naam = $parts[0] ?? '';
						$rol  = $parts[1] ?? '';
						$att  = isset( $parts[2] ) ? intval( $parts[2] ) : 0;
						if ( empty( $naam ) ) { continue; }
						$leden[] = array(
							'name'  => $naam,
							'role'  => $rol,
							'photo' => $att ? wp_get_attachment_image_url( $att, 'medium' ) : '',
						);
					}
				}
				?>

				<h2 class="org-content__title"><?php echo esc_html( $organen[ $active_tab ] ); ?></h2>
				<?php if ( $intro ) : ?>
					<div class="org-content__intro"><?php echo wp_kses_post( wpautop( $intro ) ); ?></div>
				<?php endif; ?>

				<?php if ( ! empty( $leden ) ) : ?>
					<div class="roster-grid">
						<?php foreach ( $leden as $p ) : ?>
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
				<?php else : ?>
					<p class="org-content__empty">
						<?php esc_html_e( 'Leden worden binnenkort toegevoegd. Beheer in WP-admin → Customizer → EBOH Organisatie.', 'eboh-v2' ); ?>
					</p>
				<?php endif; ?>
			</section>

		</div>
	</main>

	<?php
endwhile;

get_footer();
