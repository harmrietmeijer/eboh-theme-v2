<?php
/**
 * Front Page Template — EBOH v2
 *
 * Volgorde (klant-wens 29-06-2026):
 *  1. Uitgelicht nieuws (6 posts, 3-koloms, tekst onder foto)
 *  2. Sportlink-widget: volgende wedstrijd | laatste uitslag | stand (5 rijen)
 *  3. Maatschappelijk-sectie (blijft tot klant alternatief heeft)
 *  4. Lid-worden CTA (zonder diagonale rand)
 *  5. Sponsors
 *  6. MailerLite-nieuwsbrief (via footer)
 *
 * @package EBOH
 * @since 3.0.0
 */

get_header();

// Customizer-mods voor secties die op de pagina blijven.
$community_title     = get_theme_mod( 'eboh_community_title', 'EBOH Maatschappelijk' );
$community_intro     = get_theme_mod( 'eboh_community_intro', 'Voetbal is meer dan een wedstrijd. Samen met partners, scholen en buurtbewoners zet EBOH zich in voor een sterke en gezonde gemeenschap.' );
$community_link_text = get_theme_mod( 'eboh_community_link_text', 'Lees meer over onze initiatieven →' );
$community_link      = get_theme_mod( 'eboh_community_link', home_url( '/maatschappelijk' ) );

$cta_title       = get_theme_mod( 'eboh_cta_title', 'Ook lid worden?' );
$cta_text        = get_theme_mod( 'eboh_cta_text', 'Sluit je aan bij onze groeiende voetbalclub en maak deel uit van onze familie. Iedereen is welkom!' );
$cta_button_text = get_theme_mod( 'eboh_cta_button_text', 'Lid worden' );
$cta_button_link = get_theme_mod( 'eboh_cta_button_link', home_url( '/lid-worden' ) );
$cta_subtext     = get_theme_mod( 'eboh_cta_subtext', 'Contributie vanaf € 69 per jaar' );

$sponsors_title_part1 = get_theme_mod( 'eboh_sponsors_title_part1', 'Onze' );
$sponsors_title_part2 = get_theme_mod( 'eboh_sponsors_title_part2', 'Partners' );

// Sportlink-data voor de driedeling onder nieuws.
$next_match  = function_exists( 'eboh_get_next_match' )         ? eboh_get_next_match( 'EBOH 1' )         : null;
$last_result = function_exists( 'eboh_get_last_result' )        ? eboh_get_last_result( 'EBOH 1' )        : null;
// Stand van de competitie waarin de laatste uitslag viel (zit er geen poulecode
// in de Sportlink-data, dan de hoofdcompetitie). Drie rijen: EBOH 1 +/- 1.
$stand_poule = ( $last_result && ! empty( $last_result['poulecode'] ) ) ? $last_result['poulecode'] : '';
$stand_rows  = function_exists( 'eboh_get_stand_around_team' )  ? eboh_get_stand_around_team( 'EBOH 1', 3, $stand_poule ) : array();
$stand_title = ( $stand_poule !== '' && ! empty( $last_result['competitie'] ) )
    ? eboh_v2_competition_label( $last_result['competitie'] ) . ' - ' . __( 'Stand', 'eboh-v2' )
    : __( 'Stand', 'eboh-v2' );
$competition_logo = get_theme_mod( 'eboh_competition_logo' );
$competition_logo_url = $competition_logo ? wp_get_attachment_image_url( $competition_logo, 'medium' ) : '';
?>

<!-- ============================================
     1. UITGELICHT NIEUWS — 6 posts, 3-koloms
     ============================================ -->
<section class="news-section">
    <div class="news-section__container">
        <h2 class="news-section__title"><?php esc_html_e( 'Laatste nieuws', 'eboh-v2' ); ?></h2>
        <div class="news-grid">
            <?php
            $news_query = new WP_Query( array(
                'posts_per_page'      => 6,
                'post_type'           => 'post',
                'orderby'             => 'date',
                'order'               => 'DESC',
                'ignore_sticky_posts' => true,
            ) );
            if ( $news_query->have_posts() ) {
                while ( $news_query->have_posts() ) {
                    $news_query->the_post();
                    get_template_part( 'parts/news-card' );
                }
                wp_reset_postdata();
            }
            ?>
        </div>
        <div class="news-section__more">
            <a class="btn" href="<?php echo esc_url( home_url( '/nieuws' ) ); ?>"><?php esc_html_e( 'Alle artikelen', 'eboh-v2' ); ?></a>
        </div>
    </div>
</section>

<!-- ============================================
     2. SPORTLINK WIDGET — volgende wedstrijd | uitslag | stand
     ============================================ -->
<section class="match-widget match-widget--home" id="eboh-1">
    <div class="match-widget__container">
        <div class="match-widget__header">
            <h2 class="match-widget__title"><?php esc_html_e( 'EBOH 1', 'eboh-v2' ); ?></h2>
            <a class="match-widget__more" href="<?php echo esc_url( home_url( '/programma' ) ); ?>"><?php esc_html_e( 'Bekijk meer', 'eboh-v2' ); ?> →</a>
        </div>

        <div class="hw-grid">
            <?php
            // Kleine hulpfunctie voor één team: logo (of letter) + drieletterafkorting.
            $hw_team = function ( $naam ) {
                $logo = function_exists( 'eboh_v2_team_logo' ) ? eboh_v2_team_logo( $naam ) : '';
                ?>
                <div class="hw-team" title="<?php echo esc_attr( $naam ); ?>">
                    <div class="hw-crest" aria-hidden="true">
                        <?php if ( $logo ) : ?>
                            <img src="<?php echo esc_url( $logo ); ?>" alt="" loading="lazy">
                        <?php else : ?>
                            <span><?php echo esc_html( mb_substr( $naam, 0, 1 ) ); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="hw-team__abbr"><?php echo esc_html( eboh_v2_team_abbr( $naam ) ); ?></div>
                    <span class="hw-visually-hidden"><?php echo esc_html( $naam ); ?></span>
                </div>
                <?php
            };
            // Competitie-label onder een kolom: eigen icoon (Customizer) of alleen tekst.
            $hw_comp = function ( $naam ) use ( $competition_logo_url ) {  // icoon per competitie, anders het Customizer-logo
                $label = eboh_v2_competition_label( $naam );
                $icon  = eboh_v2_competition_icon_url( $naam );
                if ( ! $icon ) { $icon = $competition_logo_url; }
                if ( $label === '' && ! $icon ) { return; }
                ?>
                <div class="hw-comp">
                    <?php if ( $icon ) : ?>
                        <img src="<?php echo esc_url( $icon ); ?>" alt="" class="hw-comp__icon">
                    <?php endif; ?>
                    <?php if ( $label !== '' ) : ?>
                        <span class="hw-comp__label"><?php echo esc_html( $label ); ?></span>
                    <?php endif; ?>
                </div>
                <?php
            };
            ?>

            <!-- 2a. Volgende wedstrijd -->
            <article class="hw-col hw-col--next">
                <h3 class="hw-col__title"><?php esc_html_e( 'Volgende wedstrijd', 'eboh-v2' ); ?></h3>
                <?php if ( $next_match ) : ?>
                    <div class="hw-col__meta">
                        <div><?php echo esc_html( $next_match['datum_tijd'] ); ?></div>
                        <?php if ( ! empty( $next_match['locatie'] ) ) : ?>
                            <div class="hw-col__venue"><?php echo esc_html( $next_match['locatie'] ); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="hw-match">
                        <?php $hw_team( $next_match['thuisteam'] ); ?>
                        <div class="hw-match__mid"><span class="hw-match__dash">-</span></div>
                        <?php $hw_team( $next_match['uitteam'] ); ?>
                    </div>
                    <?php $hw_comp( $next_match['competitie'] ); ?>
                <?php else : ?>
                    <div class="hw-empty"><?php esc_html_e( 'Geen wedstrijd gepland', 'eboh-v2' ); ?></div>
                <?php endif; ?>
            </article>

            <!-- 2b. Laatste uitslag -->
            <article class="hw-col hw-col--result">
                <h3 class="hw-col__title"><?php esc_html_e( 'Laatste uitslag', 'eboh-v2' ); ?></h3>
                <?php if ( $last_result ) : ?>
                    <div class="hw-col__meta">
                        <div><?php echo esc_html( $last_result['datum_tijd'] ); ?></div>
                        <?php if ( ! empty( $last_result['locatie'] ) ) : ?>
                            <div class="hw-col__venue"><?php echo esc_html( $last_result['locatie'] ); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="hw-match">
                        <?php $hw_team( $last_result['thuisteam'] ); ?>
                        <div class="hw-match__mid">
                            <?php if ( '' !== (string) $last_result['score_thuis'] && '' !== (string) $last_result['score_uit'] ) : ?>
                                <span class="hw-match__score"><?php echo esc_html( $last_result['score_thuis'] . ' - ' . $last_result['score_uit'] ); ?></span>
                            <?php else : ?>
                                <span class="hw-match__dash">-</span>
                            <?php endif; ?>
                        </div>
                        <?php $hw_team( $last_result['uitteam'] ); ?>
                    </div>
                    <?php $hw_comp( $last_result['competitie'] ); ?>
                <?php else : ?>
                    <div class="hw-empty"><?php esc_html_e( 'Nog geen uitslagen', 'eboh-v2' ); ?></div>
                <?php endif; ?>
            </article>

            <!-- 2c. Stand (3 rijen, EBOH 1 in het midden) -->
            <article class="hw-col hw-col--stand">
                <h3 class="hw-col__title"><?php echo esc_html( $stand_title ); ?></h3>
                <?php if ( ! empty( $stand_rows ) ) : ?>
                    <table class="hw-stand">
                        <thead>
                            <tr>
                                <th class="hw-stand__pos">#</th>
                                <th class="hw-stand__team"><?php esc_html_e( 'Team', 'eboh-v2' ); ?></th>
                                <th>GS</th>
                                <th>W</th>
                                <th>G</th>
                                <th>V</th>
                                <th>+/-</th>
                                <th>PNT</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $stand_rows as $row ) :
                                $naam      = isset( $row['teamnaam'] ) ? $row['teamnaam'] : ( isset( $row['naam'] ) ? $row['naam'] : '' );
                                $gespeeld  = isset( $row['gespeeldewedstrijden'] ) ? $row['gespeeldewedstrijden'] : ( isset( $row['aantalgespeeld'] ) ? $row['aantalgespeeld'] : '0' );
                                $gewonnen  = isset( $row['gewonnen'] ) ? $row['gewonnen'] : ( isset( $row['aantalgewonnen'] ) ? $row['aantalgewonnen'] : 0 );
                                $gelijk    = isset( $row['gelijk'] ) ? $row['gelijk'] : ( isset( $row['aantalgelijk'] ) ? $row['aantalgelijk'] : 0 );
                                $verloren  = isset( $row['verloren'] ) ? $row['verloren'] : ( isset( $row['aantalverloren'] ) ? $row['aantalverloren'] : 0 );
                                $dv        = isset( $row['doelpuntenvoor'] ) ? (int) $row['doelpuntenvoor'] : 0;
                                $dt        = isset( $row['doelpuntentegen'] ) ? (int) $row['doelpuntentegen'] : 0;
                                $gd        = $dv - $dt;
                                $punten    = isset( $row['totaalpunten'] ) ? $row['totaalpunten'] : ( isset( $row['punten'] ) ? $row['punten'] : '0' );
                                $positie   = isset( $row['positie'] ) ? $row['positie'] : '';
                                $is_target = ! empty( $row['_is_target'] );
                                $rij_logo  = function_exists( 'eboh_v2_team_logo' ) ? eboh_v2_team_logo( $naam ) : '';
                                ?>
                                <tr<?php echo $is_target ? ' class="is-target"' : ''; ?>>
                                    <td class="hw-stand__pos"><?php echo esc_html( $positie ); ?></td>
                                    <td class="hw-stand__team" title="<?php echo esc_attr( $naam ); ?>">
                                        <span class="hw-stand__crest" aria-hidden="true">
                                            <?php if ( $rij_logo ) : ?>
                                                <img src="<?php echo esc_url( $rij_logo ); ?>" alt="" loading="lazy">
                                            <?php else : ?>
                                                <span><?php echo esc_html( mb_substr( $naam, 0, 1 ) ); ?></span>
                                            <?php endif; ?>
                                        </span>
                                        <span class="hw-stand__abbr"><?php echo esc_html( eboh_v2_team_abbr( $naam ) ); ?></span>
                                        <span class="hw-visually-hidden"><?php echo esc_html( $naam ); ?></span>
                                    </td>
                                    <td><?php echo esc_html( $gespeeld ); ?></td>
                                    <td><?php echo esc_html( $gewonnen ); ?></td>
                                    <td><?php echo esc_html( $gelijk ); ?></td>
                                    <td><?php echo esc_html( $verloren ); ?></td>
                                    <td><?php echo esc_html( ( $gd >= 0 ? '+' : '' ) . $gd ); ?></td>
                                    <td class="hw-stand__pts"><?php echo esc_html( $punten ); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <div class="hw-empty"><?php esc_html_e( 'Stand nog niet beschikbaar', 'eboh-v2' ); ?></div>
                <?php endif; ?>
            </article>
        </div>
    </div>
</section>

<!-- ============================================
     3. MAATSCHAPPELIJK (blijft tot alternatief bekend is)
     ============================================ -->
<section class="community-section">
    <div class="community-section__container">
        <div class="community-section__intro">
            <span class="community-section__eyebrow"><?php esc_html_e( 'Maatschappelijk', 'eboh-v2' ); ?></span>
            <h2 class="community-section__title"><?php echo esc_html( $community_title ); ?></h2>
            <p class="community-section__text"><?php echo esc_html( $community_intro ); ?></p>
            <a class="community-section__link" href="<?php echo esc_url( $community_link ); ?>">
                <?php echo esc_html( $community_link_text ); ?>
            </a>
        </div>
        <div class="community-section__pillars">
            <div class="community-pillar">
                <h3 class="community-pillar__title"><?php esc_html_e( 'Jeugd & School', 'eboh-v2' ); ?></h3>
                <p class="community-pillar__text"><?php esc_html_e( 'Schoolvoetbal, naschoolse trainingen en clinics voor kinderen uit de buurt.', 'eboh-v2' ); ?></p>
            </div>
            <div class="community-pillar">
                <h3 class="community-pillar__title"><?php esc_html_e( 'Samen Sterk', 'eboh-v2' ); ?></h3>
                <p class="community-pillar__text"><?php esc_html_e( 'Activiteiten met lokale partners die iedereen in beweging brengen.', 'eboh-v2' ); ?></p>
            </div>
            <div class="community-pillar">
                <h3 class="community-pillar__title"><?php esc_html_e( 'Gezond & Welzijn', 'eboh-v2' ); ?></h3>
                <p class="community-pillar__text"><?php esc_html_e( 'Initiatieven rond gezonde kantine, fair play en mentale weerbaarheid.', 'eboh-v2' ); ?></p>
            </div>
        </div>
    </div>
</section>

<!-- ============================================
     4. CTA "Lid worden" — geen diagonal, rechte rand
     ============================================ -->
<section class="cta-section">
    <div class="cta-section__content">
        <h2 class="cta-section__title"><?php echo esc_html( $cta_title ); ?></h2>
        <p class="cta-section__subtitle"><?php echo esc_html( $cta_text ); ?></p>
        <div class="cta-section__button">
            <a class="btn filled" href="<?php echo esc_url( $cta_button_link ); ?>"><?php echo esc_html( $cta_button_text ); ?></a>
        </div>
        <p class="cta-section__subtext"><?php echo esc_html( $cta_subtext ); ?></p>
    </div>
</section>

<!-- ============================================
     5. SPONSORS
     ============================================ -->
<section class="sponsors-section">
    <div class="sponsors-section__container">
        <h2 class="sponsors-section__title">
            <span class="sponsors-section__title-part1"><?php echo esc_html( $sponsors_title_part1 ); ?></span>
            <span class="sponsors-section__title-part2"><?php echo esc_html( $sponsors_title_part2 ); ?></span>
        </h2>

        <div class="sponsors-tier">
            <p class="sponsors-tier__label"><?php esc_html_e( 'Hoofdsponsor', 'eboh-v2' ); ?></p>
            <div class="sponsors-row">
                <div class="sponsor-logo">
                    <div class="sponsor-placeholder"><?php esc_html_e( 'Sponsor 1', 'eboh-v2' ); ?></div>
                </div>
            </div>
        </div>

        <div class="sponsors-tier">
            <p class="sponsors-tier__label"><?php esc_html_e( 'Partners', 'eboh-v2' ); ?></p>
            <div class="sponsors-row">
                <div class="sponsors-marquee">
                    <?php
                    $sponsors = array( 'Partner A', 'Partner B', 'Partner C', 'Partner D', 'Partner E', 'Partner F' );
                    foreach ( array_merge( $sponsors, $sponsors ) as $sponsor ) {
                        echo '<div class="sponsor-logo">';
                        echo '<div class="sponsor-placeholder">' . esc_html( $sponsor ) . '</div>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
