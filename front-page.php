<?php
/**
 * Front Page Template
 * @package EBOH
 * @since 2.0.0
 */

get_header();

// Get theme customizer values with sensible defaults matching demo.html
$hero_image = get_theme_mod( 'eboh_hero_image', get_template_directory_uri() . '/assets/images/hero-stadium.jpg' );
$hero_subtitle = get_theme_mod( 'eboh_hero_subtitle', 'Welkom bij' );
$hero_title = get_theme_mod( 'eboh_hero_title', 'VV EBOH' );
$hero_tagline = get_theme_mod( 'eboh_hero_tagline', 'Een voetbalclub waar passie, gemeenschap en talent samenkomen op het veld.' );
$hero_cta_text = get_theme_mod( 'eboh_hero_cta_text', 'Ontdek meer ↓' );
$hero_cta_link = get_theme_mod( 'eboh_hero_cta_link', '#volgende-thuiswedstrijd' );

// Match ticker info
$match_team1 = get_theme_mod( 'eboh_match_team1', 'EBOH 1' );
$match_team2 = get_theme_mod( 'eboh_match_team2', 'PELIKAAN 1' );
$match_date = get_theme_mod( 'eboh_match_date', 'ZATERDAG 17 MAART' );
$match_time = get_theme_mod( 'eboh_match_time', '20:30 UUR' );
$match_location = get_theme_mod( 'eboh_match_location', 'Sportpark De Bovenhoeck' );
$match_competition = get_theme_mod( 'eboh_match_competition', 'Zaterdag 2e klasse F' );
$match_team1_crest_id = get_theme_mod( 'eboh_match_team1_crest' );
$match_team2_crest_id = get_theme_mod( 'eboh_match_team2_crest' );
$match_team1_crest = $match_team1_crest_id ? wp_get_attachment_image_url( $match_team1_crest_id, 'medium' ) : '';
$match_team2_crest = $match_team2_crest_id ? wp_get_attachment_image_url( $match_team2_crest_id, 'medium' ) : '';

// Haal de eerstvolgende EBOH 1 thuiswedstrijd op via Sportlink.
// Wanneer er geen aankomende thuiswedstrijd is, tonen we expliciet een
// 'geen wedstrijd gepland'-state — Customizer-defaults gebruiken we hier
// bewust niet meer, omdat die anders verouderde data laten staan.
$has_next_home_match = false;
if ( function_exists( 'eboh_get_next_home_match' ) ) {
	$next_home_match = eboh_get_next_home_match();
	if ( is_array( $next_home_match ) && ! empty( $next_home_match['datum'] ) ) {
		$has_next_home_match = true;
		$match_team1       = ! empty( $next_home_match['thuisteam'] )  ? $next_home_match['thuisteam']  : $match_team1;
		$match_team2       = ! empty( $next_home_match['uitteam'] )    ? $next_home_match['uitteam']    : $match_team2;
		$match_date        = mb_strtoupper( $next_home_match['datum'], 'UTF-8' );
		$match_time        = ! empty( $next_home_match['tijd'] )       ? $next_home_match['tijd']       : $match_time;
		$match_competition = ! empty( $next_home_match['competitie'] ) ? $next_home_match['competitie'] : $match_competition;
		$match_location    = ! empty( $next_home_match['locatie'] )    ? $next_home_match['locatie']    : $match_location;
	}
}

// Community / maatschappelijk
$community_title = get_theme_mod( 'eboh_community_title', 'EBOH Maatschappelijk' );
$community_intro = get_theme_mod( 'eboh_community_intro', 'Voetbal is meer dan een wedstrijd. Samen met partners, scholen en buurtbewoners zet EBOH zich in voor een sterke en gezonde gemeenschap.' );
$community_link_text = get_theme_mod( 'eboh_community_link_text', 'Lees meer over onze initiatieven →' );
$community_link = get_theme_mod( 'eboh_community_link', home_url( '/maatschappelijk' ) );

// About section
$about_title = get_theme_mod( 'eboh_about_title', 'WIJ ZIJN' );
$about_title_main = get_theme_mod( 'eboh_about_title_main', 'EBOH' );
$about_text = get_theme_mod( 'eboh_about_text', 'Sinds onze oprichting staat EBOH bekend als een voetbalclub met ambitie, hartstocht en integriteit. We geloven in de kracht van voetbal om mensen samen te brengen en hen te helpen groeien—op en buiten het veld.' );
$about_link_text = get_theme_mod( 'eboh_about_link_text', 'Lees verder →' );
$about_link = get_theme_mod( 'eboh_about_link', home_url( '/over' ) );
$about_image = get_theme_mod( 'eboh_about_image', get_template_directory_uri() . '/assets/images/section-training.jpg' );

// CTA section
$cta_title = get_theme_mod( 'eboh_cta_title', 'Ook lid worden?' );
$cta_text = get_theme_mod( 'eboh_cta_text', 'Sluit je aan bij onze groeiende voetbalclub en maak deel uit van onze familie. Iedereen is welkom!' );
$cta_button_text = get_theme_mod( 'eboh_cta_button_text', 'Lid worden' );
$cta_button_link = get_theme_mod( 'eboh_cta_button_link', home_url( '/lid-worden' ) );
$cta_subtext = get_theme_mod( 'eboh_cta_subtext', 'Contributie vanaf € 69 per jaar' );

// Sponsors section
$sponsors_title_part1 = get_theme_mod( 'eboh_sponsors_title_part1', 'Onze' );
$sponsors_title_part2 = get_theme_mod( 'eboh_sponsors_title_part2', 'Partners' );

// Parallax section
$parallax_image = get_theme_mod( 'eboh_parallax_image', get_template_directory_uri() . '/assets/images/hero-supporters.jpg' );

// Seizoenstatistieken voor EBOH 1, dynamisch uit Sportlink.
// Dutch football season: aug-dec valt seizoen jaar/(jaar+1); jan-jul valt (jaar-1)/jaar.
$current_year   = intval( date( 'Y' ) );
$current_month  = intval( date( 'n' ) );
$season_label   = ( $current_month >= 8 )
    ? $current_year . '/' . ( $current_year + 1 )
    : ( $current_year - 1 ) . '/' . $current_year;

// Bij API-fouten of geen data tonen we '—' i.p.v. (verouderde) hardcoded cijfers,
// zodat bezoekers nooit fake stats te zien krijgen.
$stats_placeholder   = '—';
$stats_wedstrijden   = $stats_placeholder;
$stats_goals         = $stats_placeholder;
$stats_overwinningen = $stats_placeholder;
$stats_positie       = $stats_placeholder;
if ( function_exists( 'eboh_get_team_stats' ) ) {
    $team_stats = eboh_get_team_stats( 'EBOH 1' );
    if ( is_array( $team_stats ) ) {
        $stats_wedstrijden   = $team_stats['wedstrijden'];
        $stats_goals         = $team_stats['goals_voor'];
        $stats_overwinningen = $team_stats['gewonnen'];
        $stats_positie       = $team_stats['positie'] . 'e';
    }
}
?>

<!-- ============================================
     NEXT MATCH ANNOUNCEMENT BAR
     ============================================ -->
<section class="match-bar">
    <div class="match-content">
        <div class="match-detail"><?php esc_html_e( 'VOLGENDE THUISWEDSTRIJD EBOH 1', 'eboh' ); ?></div>
        <?php if ( $has_next_home_match ) : ?>
            <div class="match-detail"><?php echo esc_html( $match_team1 ); ?> - <?php echo esc_html( $match_team2 ); ?></div>
            <div class="match-detail"><?php echo esc_html( $match_date ); ?></div>
            <div class="match-detail"><?php echo esc_html( $match_time ); ?></div>
        <?php else : ?>
            <div class="match-detail"><?php esc_html_e( 'Geen thuiswedstrijd gepland', 'eboh' ); ?></div>
        <?php endif; ?>
    </div>
</section>

<!-- ============================================
     HERO SECTION
     ============================================ -->
<section class="hero hero--flat" style="background-image: url('<?php echo esc_url( $hero_image ); ?>');">
    <div class="hero__watermark">EBOH</div>
    <div class="hero__content">
        <p class="hero__subtitle"><?php echo esc_html( $hero_subtitle ); ?></p>
        <h1 class="hero__title"><?php echo esc_html( $hero_title ); ?></h1>
        <p class="hero__tagline"><?php echo esc_html( $hero_tagline ); ?></p>
        <div class="hero__cta">
            <a class="btn filled" href="<?php echo esc_url( $hero_cta_link ); ?>"><?php echo esc_html( $hero_cta_text ); ?></a>
        </div>
    </div>
</section>

<!-- ============================================
     NEXT MATCH WIDGET (prominent, Brighton-style)
     ============================================ -->
<section class="next-match" id="volgende-thuiswedstrijd">
    <div class="next-match__container">
        <div class="next-match__label">
            <span class="next-match__dot"></span>
            <?php esc_html_e( 'Volgende thuiswedstrijd EBOH 1', 'eboh' ); ?>
        </div>
        <?php if ( $has_next_home_match ) : ?>
        <div class="next-match__card">
            <div class="next-match__team next-match__team--home">
                <div class="next-match__crest" aria-hidden="true">
                    <?php if ( $match_team1_crest ) : ?>
                        <img src="<?php echo esc_url( $match_team1_crest ); ?>" alt="<?php echo esc_attr( $match_team1 ); ?>">
                    <?php else : ?>
                        <span><?php echo esc_html( mb_substr( $match_team1, 0, 1 ) ); ?></span>
                    <?php endif; ?>
                </div>
                <p class="next-match__team-name"><?php echo esc_html( $match_team1 ); ?></p>
                <p class="next-match__team-role"><?php esc_html_e( 'Thuis', 'eboh' ); ?></p>
            </div>
            <div class="next-match__meta">
                <p class="next-match__date"><?php echo esc_html( $match_date ); ?></p>
                <p class="next-match__time"><?php echo esc_html( $match_time ); ?></p>
                <p class="next-match__competition"><?php echo esc_html( $match_competition ); ?></p>
                <p class="next-match__location"><?php echo esc_html( $match_location ); ?></p>
            </div>
            <div class="next-match__team next-match__team--away">
                <div class="next-match__crest" aria-hidden="true">
                    <?php if ( $match_team2_crest ) : ?>
                        <img src="<?php echo esc_url( $match_team2_crest ); ?>" alt="<?php echo esc_attr( $match_team2 ); ?>">
                    <?php else : ?>
                        <span><?php echo esc_html( mb_substr( $match_team2, 0, 1 ) ); ?></span>
                    <?php endif; ?>
                </div>
                <p class="next-match__team-name"><?php echo esc_html( $match_team2 ); ?></p>
                <p class="next-match__team-role"><?php esc_html_e( 'Uit', 'eboh' ); ?></p>
            </div>
        </div>
        <?php else : ?>
        <div class="next-match__card next-match__card--empty" style="justify-content:center;text-align:center;padding:48px 24px;">
            <p class="next-match__empty" style="margin:0;font-family:'Oswald',sans-serif;font-size:20px;text-transform:uppercase;letter-spacing:0.05em;color:var(--text-grey,#465058);">
                <?php esc_html_e( 'Geen aankomende thuiswedstrijd gepland', 'eboh' ); ?>
            </p>
        </div>
        <?php endif; ?>
        <div class="next-match__actions">
            <a class="btn filled" href="<?php echo esc_url( home_url( '/programma' ) ); ?>"><?php esc_html_e( 'Programma', 'eboh' ); ?></a>
            <a class="btn" href="<?php echo esc_url( home_url( '/programma' ) ); ?>"><?php esc_html_e( 'Uitslagen & stand', 'eboh' ); ?></a>
        </div>
    </div>
</section>

<!-- ============================================
     NEWS SECTION (overlapping hero)
     ============================================ -->
<section class="news-section">
    <div class="news-section__container">
        <h2 class="news-section__title"><?php esc_html_e( 'Laatste nieuws', 'eboh' ); ?></h2>
        <div class="news-grid">
            <?php
            $news_args = array(
                'posts_per_page' => 3,
                'post_type'      => 'post',
                'orderby'        => 'date',
                'order'          => 'DESC',
            );

            $news_query = new WP_Query( $news_args );

            if ( $news_query->have_posts() ) {
                while ( $news_query->have_posts() ) {
                    $news_query->the_post();
                    get_template_part( 'parts/news-card' );
                }
            } else {
                // Demo fallback content
                for ( $i = 1; $i <= 3; $i++ ) {
                    echo '<a href="#" class="news-card fade-in-up" style="background-image: url(' . esc_url( get_template_directory_uri() . '/assets/images/news-wedstrijd.jpg' ) . ');">';
                    echo '<div class="news-card__content">';
                    echo '<span class="news-card__tag">' . ( $i === 1 ? 'Wedstrijd' : ( $i === 2 ? 'Jeugd' : 'Club' ) ) . '</span>';
                    echo '<p class="news-card__date">' . esc_html( date( 'd M Y', strtotime( '-' . ( $i * 2 ) . ' days' ) ) ) . '</p>';
                    echo '<h3 class="news-card__title">EBOH ' . esc_html( $i ) . ' - Artikel Titel</h3>';
                    echo '<p class="news-card__excerpt">Dit is een voorbeeldstuk tekst voor het nieuwsbericht. Vervang dit met echte inhoud via het beheerders dashboard.</p>';
                    echo '</div>';
                    echo '</a>';
                }
            }

            wp_reset_postdata();
            ?>
        </div>
    </div>
</section>

<!-- ============================================
     MAATSCHAPPELIJK / COMMUNITY SECTION
     ============================================ -->
<section class="community-section">
    <div class="community-section__container">
        <div class="community-section__intro">
            <span class="community-section__eyebrow"><?php esc_html_e( 'Maatschappelijk', 'eboh' ); ?></span>
            <h2 class="community-section__title"><?php echo esc_html( $community_title ); ?></h2>
            <p class="community-section__text"><?php echo esc_html( $community_intro ); ?></p>
            <a class="community-section__link" href="<?php echo esc_url( $community_link ); ?>">
                <?php echo esc_html( $community_link_text ); ?>
            </a>
        </div>
        <div class="community-section__pillars">
            <div class="community-pillar">
                <h3 class="community-pillar__title"><?php esc_html_e( 'Jeugd & School', 'eboh' ); ?></h3>
                <p class="community-pillar__text"><?php esc_html_e( 'Schoolvoetbal, naschoolse trainingen en clinics voor kinderen uit de buurt.', 'eboh' ); ?></p>
            </div>
            <div class="community-pillar">
                <h3 class="community-pillar__title"><?php esc_html_e( 'Samen Sterk', 'eboh' ); ?></h3>
                <p class="community-pillar__text"><?php esc_html_e( 'Activiteiten met lokale partners die iedereen in beweging brengen.', 'eboh' ); ?></p>
            </div>
            <div class="community-pillar">
                <h3 class="community-pillar__title"><?php esc_html_e( 'Gezond & Welzijn', 'eboh' ); ?></h3>
                <p class="community-pillar__text"><?php esc_html_e( 'Initiatieven rond gezonde kantine, fair play en mentale weerbaarheid.', 'eboh' ); ?></p>
            </div>
        </div>
    </div>
</section>

<!-- ============================================
     WIJ ZIJN EBOH (asymmetric full-bleed)
     ============================================ -->
<section class="about-section" style="<?php echo ! empty( $about_image ) ? 'background-image: url(' . esc_url( $about_image ) . ');' : ''; ?>">
    <div class="about-section__diagonal-top"></div>
    <div class="about-section__left">
        <h2 class="about-section__title">
            <span class="about-section__title--red"><?php echo esc_html( $about_title ); ?></span>
            <span class="about-section__title--white"><?php echo esc_html( $about_title_main ); ?></span>
        </h2>
        <p class="about-section__description">
            <?php echo esc_html( $about_text ); ?>
        </p>
        <a href="<?php echo esc_url( $about_link ); ?>" class="about-section__link">
            <?php echo esc_html( $about_link_text ); ?>
        </a>
    </div>
    <div class="about-section__right"></div>
    <div class="about-section__accent">E</div>
</section>

<!-- ============================================
     SEASON HIGHLIGHTS SECTION
     ============================================ -->
<section class="season-section">
    <div class="season-section__container">
        <h2 class="season-section__title">
            <span class="season-section__title-part1"><?php esc_html_e( 'Seizoen', 'eboh' ); ?></span>
            <span class="season-section__title-part2"><?php echo esc_html( $season_label ); ?></span>
        </h2>
        <p class="season-section__subtitle"><?php esc_html_e( 'Een seizoen vol hoogtepunten', 'eboh' ); ?></p>

        <div class="stats-row">
            <div class="stat-block">
                <div class="stat-number"><?php echo esc_html( $stats_wedstrijden ); ?></div>
                <div class="stat-label"><?php esc_html_e( 'Wedstrijden', 'eboh' ); ?></div>
            </div>
            <div class="stat-block">
                <div class="stat-number"><?php echo esc_html( $stats_goals ); ?></div>
                <div class="stat-label"><?php esc_html_e( 'Goals', 'eboh' ); ?></div>
            </div>
            <div class="stat-block">
                <div class="stat-number"><?php echo esc_html( $stats_overwinningen ); ?></div>
                <div class="stat-label"><?php esc_html_e( 'Overwinningen', 'eboh' ); ?></div>
            </div>
            <div class="stat-block">
                <div class="stat-number"><?php echo esc_html( $stats_positie ); ?></div>
                <div class="stat-label"><?php esc_html_e( 'Klassement', 'eboh' ); ?></div>
            </div>
        </div>

        <?php
        // Toon eerst posts uit de 'Hoogtepunt'-categorie; vallen we anders terug op
        // de 3 nieuwste posts zodat de sectie nooit leeg verschijnt zolang er nieuws is.
        $highlights_query = new WP_Query( array(
            'post_type'           => 'post',
            'posts_per_page'      => 3,
            'orderby'             => 'date',
            'order'               => 'DESC',
            'ignore_sticky_posts' => true,
            'category_name'       => 'hoogtepunt',
        ) );
        if ( ! $highlights_query->have_posts() ) {
            $highlights_query = new WP_Query( array(
                'post_type'           => 'post',
                'posts_per_page'      => 3,
                'orderby'             => 'date',
                'order'               => 'DESC',
                'ignore_sticky_posts' => true,
            ) );
        }

        $highlight_fallback_images = array(
            get_template_directory_uri() . '/assets/images/highlight-programma.jpg',
            get_template_directory_uri() . '/assets/images/section-training.jpg',
            get_template_directory_uri() . '/assets/images/section-action.jpg',
        );

        if ( $highlights_query->have_posts() ) :
            ?>
            <h3 class="highlights-title"><?php esc_html_e( 'Hoogtepunten', 'eboh' ); ?></h3>
            <div class="highlights-grid">
                <?php $hl_index = 0; while ( $highlights_query->have_posts() ) : $highlights_query->the_post(); ?>
                    <?php
                    $thumb_url = get_the_post_thumbnail_url( get_the_ID(), 'large' );
                    if ( ! $thumb_url ) {
                        $thumb_url = $highlight_fallback_images[ $hl_index % count( $highlight_fallback_images ) ];
                    }
                    $hl_index++;
                    ?>
                    <a href="<?php the_permalink(); ?>" class="highlight-card" style="background-image: url('<?php echo esc_url( $thumb_url ); ?>');">
                        <div class="highlight-card__content">
                            <div class="highlight-card__date"><?php echo esc_html( get_the_date( 'j F Y' ) ); ?></div>
                            <h4 class="highlight-card__title"><?php the_title(); ?></h4>
                            <p class="highlight-card__description"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 18, '…' ) ); ?></p>
                        </div>
                    </a>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
            <?php
        endif;
        ?>
    </div>
</section>

<!-- ============================================
     CTA - "OOK LID WORDEN?" section
     ============================================ -->
<section class="cta-section">
    <div class="cta-section__diagonal-top"></div>
    <div class="cta-section__content">
        <h2 class="cta-section__title"><?php echo esc_html( $cta_title ); ?></h2>
        <p class="cta-section__subtitle"><?php echo esc_html( $cta_text ); ?></p>
        <div class="cta-section__button">
            <button class="btn filled" onclick="window.location.href='<?php echo esc_url( $cta_button_link ); ?>';">
                <?php echo esc_html( $cta_button_text ); ?>
            </button>
        </div>
        <p class="cta-section__subtext"><?php echo esc_html( $cta_subtext ); ?></p>
    </div>
</section>

<!-- ============================================
     TEAMS SECTION (bento grid)
     ============================================ -->
<section class="teams-section">
    <div class="teams-section__container">
        <div class="teams-section__header">
            <h2 class="teams-section__title"><?php esc_html_e( 'Onze teams', 'eboh' ); ?></h2>
            <a href="<?php echo esc_url( home_url( '/teams' ) ); ?>" class="teams-section__link"><?php esc_html_e( 'Bekijk alle teams →', 'eboh' ); ?></a>
        </div>

        <div class="teams-grid">
            <?php
            $teams_args = array(
                'posts_per_page' => 6,
                'post_type'      => 'team',
                'orderby'        => 'date',
                'order'          => 'ASC',
            );

            $teams_query = new WP_Query( $teams_args );

            if ( $teams_query->have_posts() ) {
                $count = 0;
                while ( $teams_query->have_posts() ) {
                    $teams_query->the_post();
                    $thumb = get_the_post_thumbnail_url( get_the_ID(), 'full' ) ?: get_template_directory_uri() . '/assets/images/team-1.jpg';
                    echo '<a href="' . esc_url( get_permalink() ) . '" class="team-card" style="background-image: url(' . esc_url( $thumb ) . ');">';
                    echo '<div class="team-card__content">';
                    echo '<h3 class="team-card__name">' . esc_html( get_the_title() ) . '</h3>';
                    echo '</div>';
                    echo '</a>';
                    $count++;
                }
            } else {
                // Demo fallback
                $team_names = array( 'EBOH 1', 'EBOH 2', 'EBOH 3', 'Jeugd' );
                foreach ( $team_names as $i => $name ) {
                    $img = get_template_directory_uri() . '/assets/images/team-' . ( $i + 1 ) . '.jpg';
                    echo '<a href="#" class="team-card" style="background-image: url(' . esc_url( $img ) . ');">';
                    echo '<div class="team-card__content">';
                    echo '<h3 class="team-card__name">' . esc_html( $name ) . '</h3>';
                    echo '</div>';
                    echo '</a>';
                }
            }

            wp_reset_postdata();
            ?>
        </div>
    </div>
</section>

<!-- ============================================
     VOLUNTEERS SECTION ("Het Verhaal van EBOH")
     ============================================ -->
<section class="volunteers-section">
    <div class="volunteers-section__container">
        <h2 class="volunteers-section__title"><?php esc_html_e( 'Het verhaal van EBOH', 'eboh' ); ?></h2>
        <div class="volunteers-header">
            <p><?php esc_html_e( 'Ontmoet de mensen achter onze club—de vrijwilligers, trainers en staffleden die EBOH maken tot wat het is.', 'eboh' ); ?></p>
        </div>

        <div class="volunteers-scroll">
            <div class="volunteers-track">
                <?php
                // Demo volunteers
                $volunteers = array(
                    array( 'Jan Pietersen', 'Hoofdtrainer', '"Voetbal is meer dan een spel—het draait om integriteit, teamgeest en persoonlijke groei."', 'person-1.jpg' ),
                    array( 'Maria van Dijk', 'Jeugdcoördinator', '"Onze jongeren zijn de toekomst van EBOH. Hun potentiaal is ongelimiteerd."', 'person-2.jpg' ),
                    array( 'Peter Breugel', 'Clubvoorzitter', '"EBOH groeit dankzij de toewijding van al onze vrijwilligers en leden."', 'person-3.jpg' ),
                    array( 'Koen Makkinga', 'Assistent-trainer', '"Elk speelertje verdient aandacht, ondersteuning en kansen om te groeien."', 'person-1.jpg' ),
                    array( 'Sophie Hendrix', 'Facilitaire manager', '"Achter de schermen werken we hard om alles draaiende te houden."', 'person-2.jpg' ),
                    array( 'Amsterdam Ruiz', 'Communicatie', '"Storytelling van EBOH is onze passie—het verbindt onze gemeenschap."', 'person-3.jpg' ),
                );

                foreach ( $volunteers as $vol ) {
                    echo '<div class="volunteer-card">';
                    echo '<div class="volunteer-card__image" style="background-image: url(' . esc_url( get_template_directory_uri() . '/assets/images/' . $vol[3] ) . ');"></div>';
                    echo '<div class="volunteer-card__content">';
                    echo '<h3 class="volunteer-card__name">' . esc_html( $vol[0] ) . '</h3>';
                    echo '<p class="volunteer-card__role">' . esc_html( $vol[1] ) . '</p>';
                    echo '<p class="volunteer-card__quote">' . esc_html( $vol[2] ) . '</p>';
                    echo '</div>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>

        <div class="volunteers-nav">
            <button class="volunteers-nav__btn" title="<?php esc_attr_e( 'Vorige', 'eboh' ); ?>">←</button>
            <button class="volunteers-nav__btn" title="<?php esc_attr_e( 'Volgende', 'eboh' ); ?>">→</button>
        </div>
    </div>
</section>

<!-- ============================================
     SPONSORS SECTION (dark & atmospheric)
     ============================================ -->
<section class="sponsors-section">
    <div class="sponsors-section__container">
        <h2 class="sponsors-section__title">
            <span class="sponsors-section__title-part1"><?php echo esc_html( $sponsors_title_part1 ); ?></span>
            <span class="sponsors-section__title-part2"><?php echo esc_html( $sponsors_title_part2 ); ?></span>
        </h2>

        <div class="sponsors-tier">
            <p class="sponsors-tier__label"><?php esc_html_e( 'Hoofdsponsor', 'eboh' ); ?></p>
            <div class="sponsors-row">
                <div class="sponsor-logo">
                    <div class="sponsor-placeholder"><?php esc_html_e( 'Sponsor 1', 'eboh' ); ?></div>
                </div>
            </div>
        </div>

        <div class="sponsors-tier">
            <p class="sponsors-tier__label"><?php esc_html_e( 'Partners', 'eboh' ); ?></p>
            <div class="sponsors-row">
                <div class="sponsors-marquee">
                    <?php
                    $sponsors = array( 'Partner A', 'Partner B', 'Partner C', 'Partner D', 'Partner E', 'Partner F' );
                    // Show twice for seamless loop
                    foreach ( array_merge( $sponsors, $sponsors ) as $sponsor ) {
                        echo '<div class="sponsor-logo">';
                        echo '<div class="sponsor-placeholder">' . esc_html( $sponsor ) . '</div>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>

        <div class="sponsors-section__cta">
            <p class="sponsors-section__cta-text"><?php esc_html_e( 'Interesse in partnerschap? Laten we samen groeien!', 'eboh' ); ?></p>
            <button class="btn" onclick="window.location.href='<?php echo esc_url( home_url( '/sponsoring' ) ); ?>';">
                <?php esc_html_e( 'Word partner →', 'eboh' ); ?>
            </button>
        </div>
    </div>
</section>

<!-- ============================================
     PARALLAX PHOTO BREAK
     ============================================ -->
<section class="parallax-break" style="background-image: url('<?php echo esc_url( $parallax_image ); ?>');"></section>

<?php get_footer(); ?>
