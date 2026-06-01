<?php
/**
 * EBOH Theme Functions
 *
 * @package EBOH
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// =====================================================================
// THEME SETUP
// =====================================================================

add_action( 'after_setup_theme', 'eboh_setup' );

function eboh_setup() {
	// Localization text domain
	load_theme_textdomain( 'eboh', get_template_directory() . '/languages' );

	// Theme support
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		)
	);

	// Custom logo
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 200,
			'width'       => 200,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	// Custom image sizes
	add_image_size( 'eboh-hero', 1920, 600, true );
	add_image_size( 'eboh-card', 400, 300, true );
	add_image_size( 'eboh-team', 500, 600, true );
	add_image_size( 'eboh-thumbnail', 250, 250, true );

	// Navigation menus
	register_nav_menus(
		array(
			'primary' => esc_html__( 'Primary Menu', 'eboh' ),
			'footer'  => esc_html__( 'Footer Menu', 'eboh' ),
		)
	);
}

// =====================================================================
// ENQUEUE STYLES & SCRIPTS
// =====================================================================

add_action( 'wp_enqueue_scripts', 'eboh_enqueue_assets' );

function eboh_enqueue_assets() {
	$theme_uri = get_template_directory_uri();
	$theme_ver = wp_get_theme()->get( 'Version' );

	// Google Fonts
	wp_enqueue_style(
		'eboh-google-fonts',
		'https://fonts.googleapis.com/css2?family=Oswald:wght@400;700&family=Work+Sans:wght@400;500;700&family=Crimson+Text:ital@0;1&display=swap',
		array(),
		null
	);

	// Theme styles
	wp_enqueue_style(
		'eboh-style',
		$theme_uri . '/style.css',
		array(),
		$theme_ver
	);

	// Responsive styles
	wp_enqueue_style(
		'eboh-responsive',
		$theme_uri . '/assets/css/eboh-responsive.css',
		array( 'eboh-style' ),
		$theme_ver
	);

	// Main scripts
	wp_enqueue_script(
		'eboh-scripts',
		$theme_uri . '/assets/js/eboh-scripts.js',
		array( 'jquery' ),
		$theme_ver,
		true
	);

	// Localize script with AJAX data
	wp_localize_script(
		'eboh-scripts',
		'ebohData',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'homeUrl' => home_url(),
			'nonce'   => wp_create_nonce( 'eboh_nonce' ),
		)
	);

	// Defer main scripts
	add_filter( 'script_loader_tag', function( $tag, $handle ) {
		if ( 'eboh-scripts' === $handle ) {
			$tag = str_replace( '<script', '<script defer', $tag );
		}
		return $tag;
	}, 10, 2 );
}

// =====================================================================
// REMOVE EMOJI SCRIPTS
// =====================================================================

add_action( 'init', 'eboh_remove_emoji_scripts' );

function eboh_remove_emoji_scripts() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_in_email' );
}

// =====================================================================
// WIDGET AREAS
// =====================================================================

add_action( 'widgets_init', 'eboh_register_widget_areas' );

function eboh_register_widget_areas() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Primary Sidebar', 'eboh' ),
			'id'            => 'primary-sidebar',
			'description'   => esc_html__( 'Main sidebar for pages and posts', 'eboh' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);

	// Footer widget areas
	for ( $i = 1; $i <= 4; $i++ ) {
		register_sidebar(
			array(
				'name'          => sprintf( esc_html__( 'Footer %d', 'eboh' ), $i ),
				'id'            => 'footer-' . $i,
				'description'   => sprintf( esc_html__( 'Footer widget area %d', 'eboh' ), $i ),
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			)
		);
	}
}

// =====================================================================
// CUSTOMIZER SETTINGS
// =====================================================================

add_action( 'customize_register', 'eboh_customize_register' );

function eboh_customize_register( $wp_customize ) {
	// Main homepage panel
	$wp_customize->add_panel(
		'eboh_homepage',
		array(
			'title'    => esc_html__( 'EBOH Homepage Settings', 'eboh' ),
			'priority' => 10,
		)
	);

	// =====================================================================
	// HERO SECTION
	// =====================================================================
	$wp_customize->add_section(
		'eboh_hero',
		array(
			'title'    => esc_html__( 'Hero Section', 'eboh' ),
			'panel'    => 'eboh_homepage',
			'priority' => 10,
		)
	);

	$wp_customize->add_setting(
		'eboh_hero_subtitle',
		array(
			'default'           => esc_html__( 'Welkom bij', 'eboh' ),
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'eboh_hero_subtitle',
		array(
			'type'        => 'text',
			'section'     => 'eboh_hero',
			'label'       => esc_html__( 'Subtitle', 'eboh' ),
			'description' => esc_html__( 'Hero section subtitle', 'eboh' ),
		)
	);

	$wp_customize->add_setting(
		'eboh_hero_title',
		array(
			'default'           => esc_html__( 'VV EBOH', 'eboh' ),
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'eboh_hero_title',
		array(
			'type'        => 'text',
			'section'     => 'eboh_hero',
			'label'       => esc_html__( 'Title', 'eboh' ),
			'description' => esc_html__( 'Hero section main title', 'eboh' ),
		)
	);

	$wp_customize->add_setting(
		'eboh_hero_tagline',
		array(
			'default'           => esc_html__( 'Een voetbalclub waar passie, gemeenschap en talent samenkomen op het veld.', 'eboh' ),
			'sanitize_callback' => 'sanitize_textarea_field',
		)
	);
	$wp_customize->add_control(
		'eboh_hero_tagline',
		array(
			'type'        => 'textarea',
			'section'     => 'eboh_hero',
			'label'       => esc_html__( 'Tagline', 'eboh' ),
			'description' => esc_html__( 'Hero section tagline', 'eboh' ),
		)
	);

	$wp_customize->add_setting(
		'eboh_hero_cta_text',
		array(
			'default'           => esc_html__( 'Ontdek meer', 'eboh' ),
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'eboh_hero_cta_text',
		array(
			'type'        => 'text',
			'section'     => 'eboh_hero',
			'label'       => esc_html__( 'CTA Button Text', 'eboh' ),
			'description' => esc_html__( 'Call-to-action button text', 'eboh' ),
		)
	);

	$wp_customize->add_setting(
		'eboh_hero_cta_link',
		array(
			'default'           => '#nieuws',
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	$wp_customize->add_control(
		'eboh_hero_cta_link',
		array(
			'type'        => 'url',
			'section'     => 'eboh_hero',
			'label'       => esc_html__( 'CTA Button Link', 'eboh' ),
			'description' => esc_html__( 'Call-to-action button URL', 'eboh' ),
		)
	);

	$wp_customize->add_setting(
		'eboh_hero_image',
		array(
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'eboh_hero_image',
			array(
				'section' => 'eboh_hero',
				'label'   => esc_html__( 'Hero Background Image', 'eboh' ),
			)
		)
	);

	// =====================================================================
	// NEXT MATCH SECTION
	// =====================================================================
	$wp_customize->add_section(
		'eboh_next_match',
		array(
			'title'    => esc_html__( 'Next Match Widget', 'eboh' ),
			'panel'    => 'eboh_homepage',
			'priority' => 15,
		)
	);

	$match_text_fields = array(
		'eboh_match_team1'       => array( 'EBOH 1', __( 'Home Team Name', 'eboh' ) ),
		'eboh_match_team2'       => array( 'PELIKAAN 1', __( 'Away Team Name', 'eboh' ) ),
		'eboh_match_date'        => array( 'ZATERDAG 17 MAART', __( 'Match Date', 'eboh' ) ),
		'eboh_match_time'        => array( '20:30 UUR', __( 'Match Time', 'eboh' ) ),
		'eboh_match_competition' => array( 'Zaterdag 2e klasse F', __( 'Competition', 'eboh' ) ),
		'eboh_match_location'    => array( 'Sportpark De Bovenhoeck', __( 'Location', 'eboh' ) ),
	);
	foreach ( $match_text_fields as $id => $info ) {
		$wp_customize->add_setting( $id, array(
			'default'           => $info[0],
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( $id, array(
			'type'    => 'text',
			'section' => 'eboh_next_match',
			'label'   => $info[1],
		) );
	}

	// Team 1 (home) crest image
	$wp_customize->add_setting( 'eboh_match_team1_crest', array(
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'eboh_match_team1_crest',
			array(
				'section'     => 'eboh_next_match',
				'label'       => esc_html__( 'Home Team Crest', 'eboh' ),
				'description' => esc_html__( 'Optional club logo (leave empty for initial letter).', 'eboh' ),
			)
		)
	);

	// Team 2 (away) crest image
	$wp_customize->add_setting( 'eboh_match_team2_crest', array(
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'eboh_match_team2_crest',
			array(
				'section'     => 'eboh_next_match',
				'label'       => esc_html__( 'Away Team Crest', 'eboh' ),
				'description' => esc_html__( 'Optional opponent logo (leave empty for initial letter).', 'eboh' ),
			)
		)
	);

	// =====================================================================
	// ABOUT SECTION
	// =====================================================================
	$wp_customize->add_section(
		'eboh_about',
		array(
			'title'    => esc_html__( 'About Section', 'eboh' ),
			'panel'    => 'eboh_homepage',
			'priority' => 20,
		)
	);

	$wp_customize->add_setting(
		'eboh_about_title',
		array(
			'default'           => esc_html__( 'Wij zijn vv EBOH', 'eboh' ),
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'eboh_about_title',
		array(
			'type'        => 'text',
			'section'     => 'eboh_about',
			'label'       => esc_html__( 'About Title', 'eboh' ),
			'description' => esc_html__( 'Section title', 'eboh' ),
		)
	);

	$wp_customize->add_setting(
		'eboh_about_text',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_textarea_field',
		)
	);
	$wp_customize->add_control(
		'eboh_about_text',
		array(
			'type'        => 'textarea',
			'section'     => 'eboh_about',
			'label'       => esc_html__( 'About Text', 'eboh' ),
			'description' => esc_html__( 'Section content', 'eboh' ),
		)
	);

	$wp_customize->add_setting(
		'eboh_about_image',
		array(
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'eboh_about_image',
			array(
				'section' => 'eboh_about',
				'label'   => esc_html__( 'About Image', 'eboh' ),
			)
		)
	);

	$wp_customize->add_setting(
		'eboh_about_cta_text',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'eboh_about_cta_text',
		array(
			'type'        => 'text',
			'section'     => 'eboh_about',
			'label'       => esc_html__( 'CTA Button Text', 'eboh' ),
			'description' => esc_html__( 'Call-to-action button text', 'eboh' ),
		)
	);

	$wp_customize->add_setting(
		'eboh_about_cta_link',
		array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	$wp_customize->add_control(
		'eboh_about_cta_link',
		array(
			'type'        => 'url',
			'section'     => 'eboh_about',
			'label'       => esc_html__( 'CTA Button Link', 'eboh' ),
			'description' => esc_html__( 'Call-to-action button URL', 'eboh' ),
		)
	);

	// =====================================================================
	// CTA BANNER SECTION
	// =====================================================================
	$wp_customize->add_section(
		'eboh_cta',
		array(
			'title'    => esc_html__( 'CTA Banner Section', 'eboh' ),
			'panel'    => 'eboh_homepage',
			'priority' => 30,
		)
	);

	$wp_customize->add_setting(
		'eboh_cta_title',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'eboh_cta_title',
		array(
			'type'        => 'text',
			'section'     => 'eboh_cta',
			'label'       => esc_html__( 'CTA Title', 'eboh' ),
			'description' => esc_html__( 'Banner title', 'eboh' ),
		)
	);

	$wp_customize->add_setting(
		'eboh_cta_text',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_textarea_field',
		)
	);
	$wp_customize->add_control(
		'eboh_cta_text',
		array(
			'type'        => 'textarea',
			'section'     => 'eboh_cta',
			'label'       => esc_html__( 'CTA Text', 'eboh' ),
			'description' => esc_html__( 'Banner description text', 'eboh' ),
		)
	);

	$wp_customize->add_setting(
		'eboh_cta_button_text',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'eboh_cta_button_text',
		array(
			'type'        => 'text',
			'section'     => 'eboh_cta',
			'label'       => esc_html__( 'Button Text', 'eboh' ),
			'description' => esc_html__( 'Call-to-action button text', 'eboh' ),
		)
	);

	$wp_customize->add_setting(
		'eboh_cta_button_link',
		array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	$wp_customize->add_control(
		'eboh_cta_button_link',
		array(
			'type'        => 'url',
			'section'     => 'eboh_cta',
			'label'       => esc_html__( 'Button Link', 'eboh' ),
			'description' => esc_html__( 'Button URL', 'eboh' ),
		)
	);

	// =====================================================================
	// PARALLAX SECTION
	// =====================================================================
	$wp_customize->add_section(
		'eboh_parallax',
		array(
			'title'    => esc_html__( 'Parallax Section', 'eboh' ),
			'panel'    => 'eboh_homepage',
			'priority' => 40,
		)
	);

	$wp_customize->add_setting(
		'eboh_parallax_image',
		array(
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'eboh_parallax_image',
			array(
				'section' => 'eboh_parallax',
				'label'   => esc_html__( 'Parallax Background Image', 'eboh' ),
			)
		)
	);

	// =====================================================================
	// CLUB INFO SECTION (Separate from homepage panel)
	// =====================================================================
	$wp_customize->add_section(
		'eboh_club_info',
		array(
			'title'    => esc_html__( 'Club Information', 'eboh' ),
			'priority' => 20,
		)
	);

	$wp_customize->add_setting(
		'eboh_club_address',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'eboh_club_address',
		array(
			'type'        => 'text',
			'section'     => 'eboh_club_info',
			'label'       => esc_html__( 'Address', 'eboh' ),
			'description' => esc_html__( 'Club street address', 'eboh' ),
		)
	);

	$wp_customize->add_setting(
		'eboh_club_zipcode',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'eboh_club_zipcode',
		array(
			'type'        => 'text',
			'section'     => 'eboh_club_info',
			'label'       => esc_html__( 'Zip Code', 'eboh' ),
			'description' => esc_html__( 'Club zip code', 'eboh' ),
		)
	);

	$wp_customize->add_setting(
		'eboh_club_city',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'eboh_club_city',
		array(
			'type'        => 'text',
			'section'     => 'eboh_club_info',
			'label'       => esc_html__( 'City', 'eboh' ),
			'description' => esc_html__( 'Club city', 'eboh' ),
		)
	);

	$wp_customize->add_setting(
		'eboh_club_phone',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'eboh_club_phone',
		array(
			'type'        => 'text',
			'section'     => 'eboh_club_info',
			'label'       => esc_html__( 'Phone', 'eboh' ),
			'description' => esc_html__( 'Club phone number', 'eboh' ),
		)
	);

	$wp_customize->add_setting(
		'eboh_club_email',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_email',
		)
	);
	$wp_customize->add_control(
		'eboh_club_email',
		array(
			'type'        => 'email',
			'section'     => 'eboh_club_info',
			'label'       => esc_html__( 'Email', 'eboh' ),
			'description' => esc_html__( 'Club email address', 'eboh' ),
		)
	);

	$wp_customize->add_setting(
		'eboh_club_facebook',
		array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	$wp_customize->add_control(
		'eboh_club_facebook',
		array(
			'type'        => 'url',
			'section'     => 'eboh_club_info',
			'label'       => esc_html__( 'Facebook URL', 'eboh' ),
			'description' => esc_html__( 'Facebook page URL', 'eboh' ),
		)
	);

	$wp_customize->add_setting(
		'eboh_club_instagram',
		array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	$wp_customize->add_control(
		'eboh_club_instagram',
		array(
			'type'        => 'url',
			'section'     => 'eboh_club_info',
			'label'       => esc_html__( 'Instagram URL', 'eboh' ),
			'description' => esc_html__( 'Instagram profile URL', 'eboh' ),
		)
	);

	$wp_customize->add_setting(
		'eboh_club_twitter',
		array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	$wp_customize->add_control(
		'eboh_club_twitter',
		array(
			'type'        => 'url',
			'section'     => 'eboh_club_info',
			'label'       => esc_html__( 'Twitter/X URL', 'eboh' ),
			'description' => esc_html__( 'Twitter/X profile URL', 'eboh' ),
		)
	);

	$wp_customize->add_setting(
		'eboh_club_youtube',
		array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	$wp_customize->add_control(
		'eboh_club_youtube',
		array(
			'type'        => 'url',
			'section'     => 'eboh_club_info',
			'label'       => esc_html__( 'YouTube URL', 'eboh' ),
			'description' => esc_html__( 'YouTube channel URL', 'eboh' ),
		)
	);

	$wp_customize->add_setting(
		'eboh_club_founded',
		array(
			'default'           => '1926',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'eboh_club_founded',
		array(
			'type'        => 'text',
			'section'     => 'eboh_club_info',
			'label'       => esc_html__( 'Founded Year', 'eboh' ),
			'description' => esc_html__( 'Year club was founded', 'eboh' ),
		)
	);
}

// =====================================================================
// HELPER FUNCTIONS
// =====================================================================

/**
 * Get club information from Customizer settings
 *
 * @return array
 */
function eboh_get_club_info() {
	return array(
		'address'  => get_theme_mod( 'eboh_club_address', '' ),
		'zipcode'  => get_theme_mod( 'eboh_club_zipcode', '' ),
		'city'     => get_theme_mod( 'eboh_club_city', '' ),
		'phone'    => get_theme_mod( 'eboh_club_phone', '' ),
		'email'    => get_theme_mod( 'eboh_club_email', '' ),
		'founded'  => get_theme_mod( 'eboh_club_founded', '1926' ),
	);
}

/**
 * Get social media links from Customizer settings
 *
 * @return array
 */
function eboh_get_social_links() {
	return array(
		'facebook'  => get_theme_mod( 'eboh_club_facebook', '' ),
		'instagram' => get_theme_mod( 'eboh_club_instagram', '' ),
		'twitter'   => get_theme_mod( 'eboh_club_twitter', '' ),
		'youtube'   => get_theme_mod( 'eboh_club_youtube', '' ),
	);
}

/**
 * Get social media SVG icon
 *
 * @param string $platform Platform name (facebook, instagram, twitter, youtube)
 * @return string SVG markup
 */
function eboh_social_svg( $platform ) {
	$svg = '';

	switch ( $platform ) {
		case 'facebook':
			$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><circle cx="12" cy="12" r="10"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm3.7 10h-2.4v7.9h-3.3V12H9v-2.6h1.6V7.7c0-1.3.3-3.3 3.3-3.3h2.6v2.4h-1.9c-.3 0-.5.2-.5.5v1.5h2.4l-.4 2.6z" fill="white"/></svg>';
			break;

		case 'instagram':
			$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M7.8 2h8.4C19.4 2 22 4.6 22 7.8v8.4a5.8 5.8 0 0 1-5.8 5.8H7.8C4.6 22 2 19.4 2 16.2V7.8A5.8 5.8 0 0 1 7.8 2m-.2 2A3.6 3.6 0 0 0 4 7.6v8.8C4 18.39 5.61 20 7.6 20h8.8a3.6 3.6 0 0 0 3.6-3.6V7.6C20 5.61 18.39 4 16.4 4H7.6m9.65 1.5a1.25 1.25 0 0 1 1.25 1.25A1.25 1.25 0 0 1 17.25 8 1.25 1.25 0 0 1 16 6.75a1.25 1.25 0 0 1 1.25-1.25M12 7a5 5 0 0 1 5 5 5 5 0 0 1-5 5 5 5 0 0 1-5-5 5 5 0 0 1 5-5m0 2a3 3 0 0 0-3 3 3 3 0 0 0 3 3 3 3 0 0 0 3-3 3 3 0 0 0-3-3z"/></svg>';
			break;

		case 'twitter':
		case 'x':
			$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24h-6.6l-5.165-6.75-5.905 6.75H2.556l7.73-8.835L1.488 2.25h6.75l4.915 6.494L18.244 2.25zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>';
			break;

		case 'youtube':
			$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>';
			break;
	}

	return $svg;
}

// =====================================================================
// BODY CLASS FILTER
// =====================================================================

add_filter( 'body_class', 'eboh_body_classes' );

function eboh_body_classes( $classes ) {
	// Add site identifier class for CSS scoping
	$classes[] = 'eboh-site';

	// Add home page class
	if ( is_front_page() ) {
		$classes[] = 'is-home';
	}

	// Add team single class
	if ( is_singular( 'team' ) ) {
		$classes[] = 'is-team-single';
	}

	return $classes;
}

// =====================================================================
// EXCERPT SETTINGS
// =====================================================================

add_filter( 'excerpt_length', 'eboh_excerpt_length' );

function eboh_excerpt_length() {
	return 20;
}

add_filter( 'excerpt_more', 'eboh_excerpt_more' );

function eboh_excerpt_more() {
	return '...';
}

// =====================================================================
// AJAX HANDLERS
// =====================================================================

add_action( 'wp_ajax_eboh_load_more_news', 'eboh_load_more_news' );
add_action( 'wp_ajax_nopriv_eboh_load_more_news', 'eboh_load_more_news' );

function eboh_load_more_news() {
	check_ajax_referer( 'eboh_nonce', 'nonce' );

	$paged   = isset( $_POST['paged'] ) ? intval( $_POST['paged'] ) : 1;
	$category = isset( $_POST['category'] ) ? intval( $_POST['category'] ) : 0;

	$args = array(
		'post_type'      => 'post',
		'posts_per_page' => 6,
		'paged'          => $paged,
	);

	if ( $category > 0 ) {
		$args['cat'] = $category;
	}

	$query = new WP_Query( $args );

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			get_template_part( 'parts/news-card' );
		}
	}

	wp_die();
}

// =====================================================================
// INCLUDE REQUIRED FILES
// =====================================================================

require_once get_template_directory() . '/inc/custom-post-types.php';
require_once get_template_directory() . '/inc/shortcodes.php';
require_once get_template_directory() . '/inc/widgets.php';
require_once get_template_directory() . '/inc/sportlink-api.php';

// =====================================================================
// MEMBERSHIP SIGNUP FORM HANDLER
// Posts to admin-post.php from template-lid-worden.php
// =====================================================================

add_action( 'admin_post_nopriv_eboh_membership_signup', 'eboh_handle_membership_form' );
add_action( 'admin_post_eboh_membership_signup',        'eboh_handle_membership_form' );

function eboh_handle_membership_form() {
	$redirect = wp_get_referer() ? wp_get_referer() : home_url( '/lid-worden' );

	if ( ! isset( $_POST['eboh_signup_nonce'] ) || ! wp_verify_nonce( $_POST['eboh_signup_nonce'], 'eboh_signup' ) ) {
		wp_safe_redirect( add_query_arg( 'eboh_signup', 'error', $redirect ) );
		exit;
	}

	$fields = array(
		'first_name'    => 'Voornaam',
		'last_name'     => 'Achternaam',
		'dob'           => 'Geboortedatum',
		'gender'        => 'Geslacht',
		'street'        => 'Adres',
		'zip'           => 'Postcode',
		'city'          => 'Woonplaats',
		'email'         => 'E-mail',
		'phone'         => 'Telefoon',
		'category'      => 'Categorie',
		'experience'    => 'Ervaring',
		'previous_club' => 'Vorige club',
		'parent_name'   => 'Naam ouder/verzorger',
		'parent_phone'  => 'Telefoon ouder/verzorger',
		'iban'          => 'IBAN',
	);

	$required = array( 'first_name', 'last_name', 'dob', 'email' );
	foreach ( $required as $req ) {
		if ( empty( $_POST[ $req ] ) ) {
			wp_safe_redirect( add_query_arg( 'eboh_signup', 'error', $redirect ) );
			exit;
		}
	}
	if ( empty( $_POST['agree_rules'] ) ) {
		wp_safe_redirect( add_query_arg( 'eboh_signup', 'error', $redirect ) );
		exit;
	}

	$lines = array();
	foreach ( $fields as $key => $label ) {
		$value = isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : '';
		$lines[] = $label . ': ' . $value;
	}
	$lines[] = 'Akkoord regels: ' . ( ! empty( $_POST['agree_rules'] ) ? 'ja' : 'nee' );
	$lines[] = 'Akkoord foto’s: ' . ( ! empty( $_POST['agree_photos'] ) ? 'ja' : 'nee' );

	$to      = get_theme_mod( 'eboh_membership_email', get_option( 'admin_email' ) );
	$subject = '[EBOH] Nieuwe aanmelding: ' . sanitize_text_field( $_POST['first_name'] ) . ' ' . sanitize_text_field( $_POST['last_name'] );
	$body    = "Nieuwe aanmelding via website:\n\n" . implode( "\n", $lines );
	$headers = array(
		'Content-Type: text/plain; charset=UTF-8',
		'Reply-To: ' . sanitize_email( $_POST['email'] ),
	);

	$sent = wp_mail( $to, $subject, $body, $headers );

	wp_safe_redirect( add_query_arg( 'eboh_signup', $sent ? 'success' : 'error', $redirect ) );
	exit;
}

/**
 * Zorg dat de standaard pagina's bestaan waarnaar de homepage en footer linken,
 * en zet er bruikbare content in. Versie-vlag voorkomt dat we de DB bij elke
 * admin-load lastigvallen. Bij een version-bump worden pagina's met de oude
 * placeholder-tekst geüpdatet; pagina's die jij in WP-admin hebt aangepast
 * blijven ongemoeid.
 */
add_action( 'admin_init', 'eboh_ensure_default_pages' );
function eboh_ensure_default_pages() {
	$version = 'v2';
	if ( get_option( 'eboh_default_pages_version' ) === $version ) {
		return;
	}

	// Oude placeholder-content uit v1. Pagina's die nog precies hierin staan,
	// gaan we automatisch overschrijven met de echte content.
	$v1_placeholders = array(
		'over'                => '<p>Hier komt het verhaal van vv EBOH. Vul deze pagina in WP-admin verder aan.</p>',
		'privacybeleid'       => '<p>Vul deze pagina in WP-admin verder aan met je privacybeleid.</p>',
		'gebruiksvoorwaarden' => '<p>Vul deze pagina in WP-admin verder aan met je gebruiksvoorwaarden.</p>',
		'cookiebeleid'        => '<p>Vul deze pagina in WP-admin verder aan met je cookiebeleid.</p>',
	);

	$pages = array(
		'over'                => array(
			'title'   => 'Over EBOH',
			'content' => eboh_default_page_content_over(),
		),
		'privacybeleid'       => array(
			'title'   => 'Privacybeleid',
			'content' => eboh_default_page_content_privacybeleid(),
		),
		'gebruiksvoorwaarden' => array(
			'title'   => 'Gebruiksvoorwaarden',
			'content' => eboh_default_page_content_gebruiksvoorwaarden(),
		),
		'cookiebeleid'        => array(
			'title'   => 'Cookiebeleid',
			'content' => eboh_default_page_content_cookiebeleid(),
		),
	);

	foreach ( $pages as $slug => $data ) {
		$existing = get_page_by_path( $slug );
		if ( ! $existing ) {
			wp_insert_post( array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => $data['title'],
				'post_name'    => $slug,
				'post_content' => $data['content'],
			) );
			continue;
		}
		// Bestaat al — alleen overschrijven als de pagina nog letterlijk de
		// oude placeholder bevat (dus niet handmatig is aangepast).
		$current = trim( $existing->post_content );
		$placeholder = isset( $v1_placeholders[ $slug ] ) ? trim( $v1_placeholders[ $slug ] ) : '';
		if ( $placeholder !== '' && $current === $placeholder ) {
			wp_update_post( array(
				'ID'           => $existing->ID,
				'post_content' => $data['content'],
			) );
		}
	}

	update_option( 'eboh_default_pages_version', $version );
	// Oude v1-flag mag weg.
	delete_option( 'eboh_default_pages_created_v1' );
}

/**
 * HTML-content voor de Over-pagina. Algemene tekst over vv EBOH; jij kunt 'm
 * in WP-admin verder aanvullen met specifieke club-details (oprichtingsjaar,
 * bestuur, prestaties).
 */
function eboh_default_page_content_over() {
	return <<<'HTML'
<h2>Welkom bij vv EBOH</h2>
<p>vv EBOH is een voetbalvereniging in Dordrecht waar passie, gemeenschap en talent samenkomen op het veld. Van onze jongste pupillen tot het eerste elftal: bij EBOH staat sportief plezier en sociale verbondenheid centraal.</p>

<h3>Waar we voor staan</h3>
<p>Voetbal is bij ons meer dan een wedstrijd. We willen een club zijn waar iedereen — speler, ouder, vrijwilliger of supporter — zich thuis voelt. Respect, sportiviteit en samen genieten van het spel zijn daarvoor de basis.</p>

<h3>Onze normen en waarden</h3>
<p>EBOH werkt met een vastgelegd kader van normen en waarden, zodat iedereen weet wat we van elkaar verwachten op en rond het veld. Het volledige document is op te vragen bij het bestuur.</p>

<h3>Sportpark De Bovenhoeck</h3>
<p>Onze thuishaven is Sportpark De Bovenhoeck in Dordrecht. Hier worden onze wedstrijden en trainingen gespeeld, en is ook ons clubhuis te vinden voor een drankje en napraten na de wedstrijd.</p>

<h3>Lid worden?</h3>
<p>Wil je meespelen, meedraaien of meekijken? Bekijk onze <a href="/lid-worden">lidmaatschap-pagina</a> of neem <a href="/contact">contact</a> met ons op. Iedereen is welkom.</p>
HTML;
}

/**
 * HTML-content voor het privacybeleid. Generieke AVG-template voor een
 * amateursportclub — pas in WP-admin aan met de specifieke gegevens van
 * jouw vereniging (verwerkingsdoeleinden, bewaartermijnen, contactpersoon).
 */
function eboh_default_page_content_privacybeleid() {
	return <<<'HTML'
<p><em>Laatst bijgewerkt: vul aan in WP-admin.</em></p>

<p>vv EBOH hecht waarde aan de bescherming van jouw persoonsgegevens. In dit privacybeleid leggen we uit welke gegevens we verwerken, met welk doel, hoe lang we ze bewaren en welke rechten je hebt op grond van de Algemene verordening gegevensbescherming (AVG).</p>

<h2>Welke gegevens verwerken wij?</h2>
<p>Afhankelijk van jouw relatie tot de club kunnen wij de volgende gegevens verwerken: voor- en achternaam, geboortedatum, adres, telefoonnummer, e-mailadres, KNVB-relatienummer, gegevens over teamlidmaatschap en betalingen, en — bij toestemming — beeldmateriaal van wedstrijden en evenementen.</p>

<h2>Doel van de verwerking</h2>
<p>We gebruiken deze gegevens onder andere voor: ledenadministratie, contributie-inning, communicatie over wedstrijden en clubactiviteiten, aanmelding bij de KNVB, en het delen van clubnieuws op website en sociale media.</p>

<h2>Bewaartermijn</h2>
<p>Persoonsgegevens worden niet langer bewaard dan strikt nodig voor de hierboven genoemde doelen, of zolang de wet dat voorschrijft (bijvoorbeeld voor de fiscale administratie).</p>

<h2>Delen met derden</h2>
<p>Gegevens worden uitsluitend gedeeld met partijen die voor de uitvoering van de ledenadministratie nodig zijn, zoals de KNVB en eventueel ICT-leveranciers. Met deze partijen zijn afspraken gemaakt over zorgvuldige omgang met persoonsgegevens.</p>

<h2>Jouw rechten</h2>
<p>Je hebt het recht om jouw persoonsgegevens in te zien, te corrigeren of te laten verwijderen. Ook kun je bezwaar maken tegen de verwerking of een verzoek tot beperking indienen. Mail hiervoor naar de ledenadministratie.</p>

<h2>Cookies</h2>
<p>Onze website maakt gebruik van cookies. Zie het <a href="/cookiebeleid">Cookiebeleid</a> voor meer informatie.</p>

<h2>Vragen of klachten</h2>
<p>Heb je vragen over dit privacybeleid of denk je dat we niet zorgvuldig met jouw gegevens omgaan? Neem dan <a href="/contact">contact</a> met ons op. Je hebt daarnaast altijd het recht een klacht in te dienen bij de Autoriteit Persoonsgegevens.</p>

<p><strong>Let op:</strong> dit is een algemene template. Laat deze tekst nakijken door iemand met juridische expertise of een specialistische tool voordat je 'm definitief publiceert.</p>
HTML;
}

/**
 * HTML-content voor de gebruiksvoorwaarden. Generieke template.
 */
function eboh_default_page_content_gebruiksvoorwaarden() {
	return <<<'HTML'
<p><em>Laatst bijgewerkt: vul aan in WP-admin.</em></p>

<p>Door deze website te gebruiken ga je akkoord met onderstaande gebruiksvoorwaarden. vv EBOH kan deze voorwaarden van tijd tot tijd aanpassen; raadpleeg deze pagina daarom regelmatig.</p>

<h2>Doel van de website</h2>
<p>Deze website biedt informatie over vv EBOH, onze teams, wedstrijdprogramma's, nieuws en lidmaatschap. De inhoud is bedoeld als algemene informatie en kan zonder voorafgaande kennisgeving worden gewijzigd.</p>

<h2>Aansprakelijkheid</h2>
<p>We doen ons best de informatie op deze site juist en actueel te houden, maar kunnen geen garantie geven op volledigheid of juistheid. vv EBOH is niet aansprakelijk voor eventuele schade die voortvloeit uit het gebruik van deze website of de informatie daarop.</p>

<h2>Intellectueel eigendom</h2>
<p>De teksten, afbeeldingen, logo's en overige content op deze site zijn eigendom van vv EBOH of de oorspronkelijke rechthebbenden. Overname is alleen toegestaan na voorafgaande schriftelijke toestemming.</p>

<h2>Externe links</h2>
<p>Op de site staan links naar websites van derden (bijvoorbeeld KNVB, Sportlink, sponsoren). vv EBOH heeft geen invloed op de inhoud van deze externe sites en is niet verantwoordelijk voor de daar aangeboden informatie.</p>

<h2>Toepasselijk recht</h2>
<p>Op deze gebruiksvoorwaarden is Nederlands recht van toepassing.</p>

<h2>Contact</h2>
<p>Vragen of opmerkingen over deze voorwaarden? Neem <a href="/contact">contact</a> met ons op.</p>

<p><strong>Let op:</strong> dit is een algemene template. Laat 'm zo nodig juridisch toetsen voordat je 'm definitief publiceert.</p>
HTML;
}

/**
 * HTML-content voor het cookiebeleid. Generiek; specialistische tooling
 * (Cookiebot, Iubenda, Complianz) is aan te raden voor een volledig
 * AVG/ePrivacy-compliant cookieoverzicht.
 */
function eboh_default_page_content_cookiebeleid() {
	return <<<'HTML'
<p><em>Laatst bijgewerkt: vul aan in WP-admin.</em></p>

<p>vv EBOH gebruikt op deze website cookies om de site goed te laten functioneren en het bezoek te analyseren. Op deze pagina leggen we uit welke cookies we gebruiken en hoe je deze kunt beheren.</p>

<h2>Wat zijn cookies?</h2>
<p>Cookies zijn kleine tekstbestanden die bij een bezoek aan een website op je apparaat worden geplaatst. Ze worden bijvoorbeeld gebruikt om voorkeuren te onthouden of om bezoekersgedrag te meten.</p>

<h2>Welke cookies gebruiken wij?</h2>
<ul>
	<li><strong>Functionele cookies:</strong> nodig voor het juist functioneren van de website (bijvoorbeeld voor de werking van formulieren of het onthouden van instellingen). Hiervoor is geen toestemming nodig.</li>
	<li><strong>Analytische cookies:</strong> we kunnen geanonimiseerde statistieken bijhouden over websitebezoek om de site te verbeteren.</li>
	<li><strong>Cookies van derden:</strong> bij ingesloten content (zoals YouTube, sociale media, Sportlink-widgets) kunnen externe partijen cookies plaatsen. Op deze cookies hebben wij geen directe invloed.</li>
</ul>

<h2>Cookies beheren</h2>
<p>Je kunt cookies altijd zelf verwijderen of blokkeren via de instellingen van je browser. Houd er rekening mee dat sommige delen van de website daardoor mogelijk niet goed werken.</p>

<h2>Wijzigingen</h2>
<p>We kunnen dit cookiebeleid aanpassen wanneer de website of wet- en regelgeving daar aanleiding toe geeft. De meest actuele versie staat altijd op deze pagina.</p>

<h2>Vragen?</h2>
<p>Voor vragen over dit cookiebeleid kun je <a href="/contact">contact</a> met ons opnemen. Zie ook ons <a href="/privacybeleid">privacybeleid</a>.</p>

<p><strong>Let op:</strong> deze tekst is een algemene template. Voor een volledig sluitend cookieoverzicht (met exacte cookienamen en aanbieders) is een tool als Cookiebot, Iubenda of Complianz aan te raden.</p>
HTML;
}

/**
 * Zorg dat de 'Hoogtepunt'-categorie bestaat voor posts. Posts in deze categorie
 * verschijnen in de Highlights-sectie op de homepage.
 */
add_action( 'init', 'eboh_ensure_highlight_category' );
function eboh_ensure_highlight_category() {
	if ( term_exists( 'hoogtepunt', 'category' ) ) {
		return;
	}
	wp_insert_term( 'Hoogtepunt', 'category', array(
		'slug'        => 'hoogtepunt',
		'description' => 'Posts in deze categorie verschijnen in de Hoogtepunten-sectie op de homepage.',
	) );
}

/**
 * Toon alle teams op één pagina, anders sorteert onze the_posts-filter alleen
 * binnen de huidige paginering en blijft de jeugd vooraan staan.
 */
add_action( 'pre_get_posts', 'eboh_team_archive_no_paging' );
function eboh_team_archive_no_paging( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( $query->is_post_type_archive( 'team' ) ) {
		$query->set( 'posts_per_page', -1 );
	}
}

/**
 * Sorteer team-archief van EBOH 1 naar jongste jeugd.
 *
 * Standaard sorteert WordPress op datum. We willen:
 *  1) Senioren (EBOH 1, EBOH 2, ...) oplopend op nummer
 *  2) Daarna jongens-jeugd (JO19, JO17, ..., JO9) van oud naar jong
 *  3) Daarna meisjes-jeugd (MO19, MO17, ...) van oud naar jong
 *  4) Onbekende namen achteraan, alfabetisch
 *
 * We sorteren in PHP na de query — eenvoudiger dan een complexe SQL ORDER BY.
 */
add_filter( 'the_posts', 'eboh_sort_team_archive', 10, 2 );
function eboh_sort_team_archive( $posts, $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return $posts;
	}
	if ( ! $query->is_post_type_archive( 'team' ) ) {
		return $posts;
	}

	usort( $posts, function ( $a, $b ) {
		$ka = eboh_team_sort_key( $a->post_title );
		$kb = eboh_team_sort_key( $b->post_title );
		// Vergelijk elementen van de sleutel-array op volgorde.
		for ( $i = 0; $i < count( $ka ); $i++ ) {
			if ( $ka[ $i ] === $kb[ $i ] ) { continue; }
			if ( is_numeric( $ka[ $i ] ) && is_numeric( $kb[ $i ] ) ) {
				return $ka[ $i ] <=> $kb[ $i ];
			}
			return strcmp( (string) $ka[ $i ], (string) $kb[ $i ] );
		}
		return 0;
	} );

	return $posts;
}

/**
 * Bouw een sorteerbare sleutel uit een teamnaam zoals 'EBOH 1', 'EBOH JO17-1',
 * 'EBOH MO13-2'. Lager = eerder in de lijst.
 */
function eboh_team_sort_key( $title ) {
	$title = strtoupper( trim( $title ) );
	$rest  = preg_replace( '/^EBOH\s+/', '', $title );

	// Senioren: alleen een nummer (EBOH 1, EBOH 2, EBOH 12).
	if ( preg_match( '/^(\d+)$/', $rest, $m ) ) {
		return array( 0, (int) $m[1], 0 );
	}

	// Jongens-jeugd: JO## of MA## (eventueel -N).
	if ( preg_match( '/^(JO|MA)(\d+)(?:-(\d+))?/', $rest, $m ) ) {
		$leeftijd = (int) $m[2];
		$volgnr   = isset( $m[3] ) ? (int) $m[3] : 1;
		// Negatieve leeftijd → oudere jeugd komt eerst (JO19 vóór JO11).
		return array( 1, -$leeftijd, $volgnr );
	}

	// Meisjes-jeugd: MO## of VR##.
	if ( preg_match( '/^(MO|VR)(\d+)(?:-(\d+))?/', $rest, $m ) ) {
		$leeftijd = (int) $m[2];
		$volgnr   = isset( $m[3] ) ? (int) $m[3] : 1;
		return array( 2, -$leeftijd, $volgnr );
	}

	// Onbekend formaat → alfabetisch achteraan.
	return array( 3, 0, $title );
}
