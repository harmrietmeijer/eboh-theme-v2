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
$stand_rows  = function_exists( 'eboh_get_stand_around_team' )  ? eboh_get_stand_around_team( 'EBOH 1', 5 ) : array();
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
<section class="match-widget" id="eboh-1">
    <div class="match-widget__container">
        <div class="match-widget__header">
            <h2 class="match-widget__title"><?php esc_html_e( 'EBOH 1', 'eboh-v2' ); ?></h2>
            <a class="match-widget__more" href="<?php echo esc_url( home_url( '/programma' ) ); ?>"><?php esc_html_e( 'Bekijk meer', 'eboh-v2' ); ?> →</a>
        </div>

        <div class="match-widget__grid">

            <!-- 2a. Volgende wedstrijd -->
            <article class="mw-card mw-card--next">
                <div class="mw-card__competition">
                    <?php if ( $competition_logo_url ) : ?>
                        <img src="<?php echo esc_url( $competition_logo_url ); ?>" alt="" class="mw-card__competition-logo">
                    <?php endif; ?>
                    <span class="mw-card__competition-name"><?php echo esc_html( $next_match['competitie'] ?? __( 'Competitie', 'eboh-v2' ) ); ?></span>
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
                            <?php if ( ! empty( $next_match['locatie'] ) ) : ?>
                                <div class="mw-card__venue"><?php echo esc_html( $next_match['locatie'] ); ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mw-card__team mw-card__team--away">
                            <div class="mw-card__crest" aria-hidden="true"><?php echo esc_html( mb_substr( $next_match['uitteam'], 0, 1 ) ); ?></div>
                            <div class="mw-card__team-name"><?php echo esc_html( $next_match['uitteam'] ); ?></div>
                        </div>
                    </div>
                    <div class="mw-card__footer">
                        <a class="mw-card__cta" href="<?php echo esc_url( home_url( '/programma' ) ); ?>"><?php esc_html_e( 'Programma', 'eboh-v2' ); ?></a>
                    </div>
                <?php else : ?>
                    <div class="mw-card__empty"><?php esc_html_e( 'Geen wedstrijd gepland', 'eboh-v2' ); ?></div>
                <?php endif; ?>
            </article>

            <!-- 2b. Laatste uitslag -->
            <article class="mw-card mw-card--result">
                <div class="mw-card__competition">
                    <?php if ( $competition_logo_url ) : ?>
                        <img src="<?php echo esc_url( $competition_logo_url ); ?>" alt="" class="mw-card__competition-logo">
                    <?php endif; ?>
                    <span class="mw-card__competition-name"><?php echo esc_html( $last_result['competitie'] ?? __( 'Uitslag', 'eboh-v2' ) ); ?></span>
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
                            <div class="mw-card__final-label"><?php esc_html_e( 'EINDSTAND', 'eboh-v2' ); ?></div>
                            <div class="mw-card__date mw-card__date--past"><?php echo esc_html( $last_result['datum_kort'] ); ?></div>
                        </div>
                        <div class="mw-card__team mw-card__team--away">
                            <div class="mw-card__crest" aria-hidden="true"><?php echo esc_html( mb_substr( $last_result['uitteam'], 0, 1 ) ); ?></div>
                            <div class="mw-card__team-name"><?php echo esc_html( $last_result['uitteam'] ); ?></div>
                        </div>
                    </div>
                    <div class="mw-card__footer">
                        <a class="mw-card__cta" href="<?php echo esc_url( home_url( '/programma' ) ); ?>"><?php esc_html_e( 'Uitslagen', 'eboh-v2' ); ?></a>
                    </div>
                <?php else : ?>
                    <div class="mw-card__empty"><?php esc_html_e( 'Nog geen uitslagen', 'eboh-v2' ); ?></div>
                <?php endif; ?>
            </article>

            <!-- 2c. Stand (5 rijen, EBOH gecentreerd) -->
            <article class="mw-card mw-card--stand">
                <div class="mw-card__competition">
                    <?php if ( $competition_logo_url ) : ?>
                        <img src="<?php echo esc_url( $competition_logo_url ); ?>" alt="" class="mw-card__competition-logo">
                    <?php endif; ?>
                    <span class="mw-card__competition-name"><?php esc_html_e( 'Stand', 'eboh-v2' ); ?></span>
                </div>
                <?php if ( ! empty( $stand_rows ) ) : ?>
                    <table class="mw-stand">
                        <thead>
                            <tr>
                                <th class="mw-stand__pos">#</th>
                                <th class="mw-stand__team"><?php esc_html_e( 'Team', 'eboh-v2' ); ?></th>
                                <th>GS</th>
                                <th>W</th>
                                <th>G</th>
                                <th>V</th>
                                <th>+/-</th>
                                <th>Pnt</th>
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
                                ?>
                                <tr<?php echo $is_target ? ' class="is-target"' : ''; ?>>
                                    <td class="mw-stand__pos"><?php echo esc_html( $positie ); ?></td>
                                    <td class="mw-stand__team"><?php echo esc_html( $naam ); ?></td>
                                    <td><?php echo esc_html( $gespeeld ); ?></td>
                                    <td><?php echo esc_html( $gewonnen ); ?></td>
                                    <td><?php echo esc_html( $gelijk ); ?></td>
                                    <td><?php echo esc_html( $verloren ); ?></td>
                                    <td><?php echo esc_html( ( $gd >= 0 ? '+' : '' ) . $gd ); ?></td>
                                    <td class="mw-stand__pts"><?php echo esc_html( $punten ); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="mw-card__footer">
                        <a class="mw-card__cta" href="<?php echo esc_url( home_url( '/programma' ) ); ?>"><?php esc_html_e( 'Volledige stand', 'eboh-v2' ); ?></a>
                    </div>
                <?php else : ?>
                    <div class="mw-card__empty"><?php esc_html_e( 'Stand nog niet beschikbaar', 'eboh-v2' ); ?></div>
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
