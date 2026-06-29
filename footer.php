<?php
/**
 * Footer Template — EBOH v2
 *
 * Volgorde: nieuwsbrief-signup → contact + socials → copyright + juridisch.
 * Compactere padding dan v1 op verzoek van de klant (29-06-2026).
 *
 * @package EBOH
 * @since 3.0.0
 */
?>

<!-- ============================================
     NIEUWSBRIEF-SIGNUP (MailerLite via eboh-mailerlite-plugin)
     ============================================ -->
<section class="newsletter-strip">
    <div class="newsletter-strip__container">
        <div class="newsletter-strip__copy">
            <h2 class="newsletter-strip__title"><?php esc_html_e( 'Blijf op de hoogte', 'eboh-v2' ); ?></h2>
            <p class="newsletter-strip__text"><?php esc_html_e( 'Schrijf je in voor de EBOH-nieuwsbrief en ontvang clubnieuws, wedstrijdupdates en aankondigingen.', 'eboh-v2' ); ?></p>
        </div>
        <div class="newsletter-strip__form">
            <?php
            // Plugin: eboh-mailerlite (Settings → EBOH MailerLite). Zonder plugin
            // tonen we niets — voorkomt dat een dood formulier blijft staan.
            if ( shortcode_exists( 'eboh_mailerlite_form' ) ) {
                echo do_shortcode( '[eboh_mailerlite_form]' );
            }
            ?>
        </div>
    </div>
</section>

<!-- ============================================
     FOOTER
     ============================================ -->
<footer class="footer">
    <div class="footer__container">
        <div class="footer__grid footer__grid--compact">

            <div class="footer__column">
                <h3 class="footer__title"><?php esc_html_e( 'Contact', 'eboh-v2' ); ?></h3>
                <div class="footer__links">
                    <?php
                    $phone = get_theme_mod( 'eboh_club_phone', '+31 6 2256 3456' );
                    $email = get_theme_mod( 'eboh_club_email', 'info@eboh.nl' );
                    ?>
                    <a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+\-]/', '', $phone ) ); ?>" class="footer__link">
                        <?php echo esc_html( $phone ); ?>
                    </a>
                    <a href="mailto:<?php echo esc_attr( $email ); ?>" class="footer__link">
                        <?php echo esc_html( $email ); ?>
                    </a>
                    <p class="footer__address">
                        <?php
                        $address  = get_theme_mod( 'eboh_club_address', 'Sportcomplex Schenkeldijk 6' );
                        $zipcode  = get_theme_mod( 'eboh_club_zipcode', '3328 LE' );
                        $city     = get_theme_mod( 'eboh_club_city', 'Dordrecht' );
                        echo esc_html( $address ) . '<br>';
                        echo esc_html( $zipcode ) . ' ' . esc_html( $city );
                        ?>
                    </p>
                </div>
            </div>

            <div class="footer__column">
                <h3 class="footer__title"><?php esc_html_e( 'Volg ons', 'eboh-v2' ); ?></h3>
                <div class="footer__socials">
                    <?php
                    $socials = array(
                        'facebook'  => array(
                            'url'   => get_theme_mod( 'eboh_social_facebook', 'https://www.facebook.com/vveboh/' ),
                            'label' => __( 'Facebook', 'eboh-v2' ),
                            'icon'  => 'facebook',
                        ),
                        'instagram' => array(
                            'url'   => get_theme_mod( 'eboh_social_instagram', 'https://www.instagram.com/vveboh/' ),
                            'label' => __( 'Instagram', 'eboh-v2' ),
                            'icon'  => 'instagram',
                        ),
                        'x'         => array(
                            'url'   => get_theme_mod( 'eboh_social_x', 'https://twitter.com/EBOHDordrecht' ),
                            'label' => __( 'X', 'eboh-v2' ),
                            'icon'  => 'x',
                        ),
                    );
                    foreach ( $socials as $s ) {
                        if ( empty( $s['url'] ) ) { continue; }
                        echo '<a href="' . esc_url( $s['url'] ) . '" class="footer__social-link footer__social-link--' . esc_attr( $s['icon'] ) . '" target="_blank" rel="noopener noreferrer" aria-label="' . esc_attr( $s['label'] ) . '">';
                        if ( function_exists( 'eboh_social_svg' ) ) {
                            echo eboh_social_svg( $s['icon'] );
                        } else {
                            echo esc_html( strtoupper( substr( $s['icon'], 0, 1 ) ) );
                        }
                        echo '</a>';
                    }
                    ?>
                </div>
            </div>

        </div>

        <div class="footer__bottom">
            <p class="footer__copyright">
                &copy; <?php echo esc_html( date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'Alle rechten voorbehouden.', 'eboh-v2' ); ?>
            </p>
            <div class="footer__bottom-links">
                <a href="<?php echo esc_url( home_url( '/privacybeleid' ) ); ?>" class="footer__bottom-link"><?php esc_html_e( 'Privacybeleid', 'eboh-v2' ); ?></a>
                <a href="<?php echo esc_url( home_url( '/gebruiksvoorwaarden' ) ); ?>" class="footer__bottom-link"><?php esc_html_e( 'Gebruiksvoorwaarden', 'eboh-v2' ); ?></a>
                <a href="<?php echo esc_url( home_url( '/cookiebeleid' ) ); ?>" class="footer__bottom-link"><?php esc_html_e( 'Cookiebeleid', 'eboh-v2' ); ?></a>
            </div>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
