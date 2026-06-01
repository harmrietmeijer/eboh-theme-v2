<?php
/**
 * Header Template
 * @package EBOH
 * @since 2.0.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- ============================================
     STICKY NAVIGATION HEADER
     ============================================ -->
<header class="site-header" id="siteHeader">
    <div class="site-header__container">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-header__logo">
            <?php
            if ( has_custom_logo() ) {
                $custom_logo_id = get_theme_mod( 'custom_logo' );
                $logo_image     = wp_get_attachment_image_src( $custom_logo_id, 'full' );
                if ( $logo_image ) {
                    echo '<div class="site-header__logo-img" style="background-image: url(' . esc_url( $logo_image[0] ) . ');"></div>';
                }
            } else {
                echo '<div class="site-header__logo-img"></div>';
            }
            ?>
            <span class="site-header__logo-text"><?php bloginfo( 'name' ); ?></span>
        </a>

        <nav class="site-nav">
            <?php
            wp_nav_menu( array(
                'theme_location' => 'primary',
                'container'      => false,
                'fallback_cb'    => function() {
                    echo '<a href="' . esc_url( home_url( '/' ) ) . '" class="site-nav__link">Home</a>';
                    echo '<a href="#" class="site-nav__link">Nieuws</a>';
                    echo '<a href="#" class="site-nav__link">Teams</a>';
                    echo '<a href="#" class="site-nav__link">Programma</a>';
                    echo '<a href="#" class="site-nav__link">Contact</a>';
                },
                'depth'          => 1,
                'link_before'    => '<span class="site-nav__link-text">',
                'link_after'     => '</span>',
                'items_wrap'     => '%3$s',
            ) );
            ?>
            <a href="<?php echo esc_url( home_url( '/lid-worden' ) ); ?>" class="site-nav__cta">
                <?php esc_html_e( 'Lid Worden', 'eboh' ); ?>
            </a>
        </nav>

        <button class="hamburger" id="hamburgerBtn" aria-label="<?php esc_attr_e( 'Menu', 'eboh' ); ?>">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>
</header>

<!-- Mobile Menu -->
<div class="mobile-menu" id="mobileMenu">
    <?php
    wp_nav_menu( array(
        'theme_location' => 'primary',
        'container'      => false,
        'fallback_cb'    => function() {
            echo '<a href="' . esc_url( home_url( '/' ) ) . '">Home</a>';
            echo '<a href="#">Nieuws</a>';
            echo '<a href="#">Teams</a>';
            echo '<a href="#">Programma</a>';
            echo '<a href="#">Contact</a>';
            echo '<a href="' . esc_url( home_url( '/lid-worden' ) ) . '">Lid Worden</a>';
        },
        'depth'          => 1,
        'items_wrap'     => '%3$s',
    ) );
    ?>
</div>
