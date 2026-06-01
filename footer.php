<?php
/**
 * Footer Template
 * @package EBOH
 * @since 2.0.0
 */
?>

<!-- ============================================
     FOOTER
     ============================================ -->
<footer class="footer">
    <div class="footer__container">
        <div class="footer__grid">
            <div class="footer__column">
                <div class="footer__logo"></div>
                <h3 class="footer__title"><?php bloginfo( 'name' ); ?></h3>
                <p class="footer__content">
                    <?php bloginfo( 'description' ); ?>
                </p>
            </div>

            <div class="footer__column">
                <h3 class="footer__title"><?php esc_html_e( 'Snelle links', 'eboh' ); ?></h3>
                <div class="footer__links">
                    <?php
                    wp_nav_menu( array(
                        'theme_location' => 'footer',
                        'container'      => false,
                        'fallback_cb'    => function() {
                            echo '<a href="#" class="footer__link">Over EBOH</a>';
                            echo '<a href="' . esc_url( home_url( '/lid-worden' ) ) . '" class="footer__link">Lid worden</a>';
                            echo '<a href="#" class="footer__link">Onze teams</a>';
                            echo '<a href="#" class="footer__link">Agenda</a>';
                            echo '<a href="#" class="footer__link">Nieuws</a>';
                        },
                        'depth'          => 1,
                        'items_wrap'     => '%3$s',
                    ) );
                    ?>
                </div>
            </div>

            <div class="footer__column">
                <h3 class="footer__title"><?php esc_html_e( 'Contact', 'eboh' ); ?></h3>
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
                    <p style="margin-top: 12px; font-size: 14px; color: rgba(255, 255, 255, 0.8);">
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
                <h3 class="footer__title"><?php esc_html_e( 'Volg ons', 'eboh' ); ?></h3>
                <div class="footer__socials">
                    <?php
                    $facebook  = get_theme_mod( 'eboh_social_facebook' );
                    $instagram = get_theme_mod( 'eboh_social_instagram' );
                    $twitter   = get_theme_mod( 'eboh_social_x' );
                    $youtube   = get_theme_mod( 'eboh_social_youtube' );

                    if ( $facebook ) {
                        echo '<a href="' . esc_url( $facebook ) . '" class="footer__social-link" title="' . esc_attr__( 'Facebook', 'eboh' ) . '">f</a>';
                    }
                    if ( $instagram ) {
                        echo '<a href="' . esc_url( $instagram ) . '" class="footer__social-link" title="' . esc_attr__( 'Instagram', 'eboh' ) . '">📷</a>';
                    }
                    if ( $twitter ) {
                        echo '<a href="' . esc_url( $twitter ) . '" class="footer__social-link" title="' . esc_attr__( 'Twitter', 'eboh' ) . '">𝕏</a>';
                    }
                    if ( $youtube ) {
                        echo '<a href="' . esc_url( $youtube ) . '" class="footer__social-link" title="' . esc_attr__( 'YouTube', 'eboh' ) . '">▶</a>';
                    }
                    ?>
                </div>
            </div>
        </div>

        <div class="footer__bottom">
            <p class="footer__copyright">
                &copy; <?php echo esc_html( date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'Alle rechten voorbehouden.', 'eboh' ); ?>
            </p>
            <div class="footer__bottom-links">
                <a href="<?php echo esc_url( home_url( '/privacybeleid' ) ); ?>" class="footer__bottom-link"><?php esc_html_e( 'Privacybeleid', 'eboh' ); ?></a>
                <a href="<?php echo esc_url( home_url( '/gebruiksvoorwaarden' ) ); ?>" class="footer__bottom-link"><?php esc_html_e( 'Gebruiksvoorwaarden', 'eboh' ); ?></a>
                <a href="<?php echo esc_url( home_url( '/cookiebeleid' ) ); ?>" class="footer__bottom-link"><?php esc_html_e( 'Cookiebeleid', 'eboh' ); ?></a>
            </div>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
