<?php
/**
 * News Card Template Part
 * Stacked layout: foto bovenop, tekst eronder. Hard edges, gelijke hoogte
 * dankzij flex-column met content-area die uitrekt.
 *
 * @package EBOH
 * @since 2.0.0
 */

$categories = get_the_category();
$thumb_url  = get_the_post_thumbnail_url( get_the_ID(), 'large' )
    ?: get_template_directory_uri() . '/assets/images/news-wedstrijd.jpg';
?>

<a href="<?php the_permalink(); ?>" class="news-card fade-in-up">
    <div class="news-card__media" style="background-image: url('<?php echo esc_url( $thumb_url ); ?>');"
         role="img"
         aria-label="<?php echo esc_attr( get_the_title() ); ?>"></div>
    <div class="news-card__body">
        <div class="news-card__meta">
            <span class="news-card__tag">
                <?php
                if ( ! empty( $categories ) ) {
                    echo esc_html( $categories[0]->name );
                } else {
                    esc_html_e( 'Nieuws', 'eboh' );
                }
                ?>
            </span>
            <span class="news-card__date"><?php echo esc_html( date_i18n( 'd M Y', strtotime( get_the_date() ) ) ); ?></span>
        </div>
        <h3 class="news-card__title"><?php the_title(); ?></h3>
        <p class="news-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?></p>
    </div>
</a>
