<?php
/**
 * Footer Template
 *
 * EBOH v2 — compacte footer zonder 'Snelle links' (top-nav volstaat) en
 * zonder oude logo-stub. Twee kolommen: clubgegevens en socials. Daaronder
 * een smal bottom-bar met copyright en juridische links.
 *
 * @package EBOH
 * @since 3.0.0
 */
?>

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
                    $facebook  = get_theme_mod( 'eboh_social_facebook' );
                    $instagram = get_theme_mod( 'eboh_social_instagram' );
                    $twitter   = get_theme_mod( 'eboh_social_x' );
                    $youtube   = get_theme_mod( 'eboh_social_youtube' );

                    if ( $facebook ) {
                        echo '<a href="' . esc_url( $facebook ) . '" class="footer__social-link" title="' . esc_attr__( 'Facebook', 'eboh-v2' ) . '">f</a>';
                    }
                    if ( $instagram ) {
                        echo '<a href="' . esc_url( $instagram ) . '" class="footer__social-link" title="' . esc_attr__( 'Instagram', 'eboh-v2' ) . '">IG</a>';
                    }
                    if ( $twitter ) {
                        echo '<a href="' . esc_url( $twitter ) . '" class="footer__social-link" title="' . esc_attr__( 'X', 'eboh-v2' ) . '">𝕏</a>';
                    }
                    if ( $youtube ) {
                        echo '<a href="' . esc_url( $youtube ) . '" class="footer__social-link" title="' . esc_attr__( 'YouTube', 'eboh-v2' ) . '">▶</a>';
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
