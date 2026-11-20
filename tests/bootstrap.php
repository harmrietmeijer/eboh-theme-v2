<?php
/**
 * Test-bootstrap zonder WordPress.
 *
 * Laadt functions.php en inc/*.php met minimale stubs, zodat pure functies
 * getest kunnen worden. Dit is bewust géén WordPress-testsuite: alles wat
 * WordPress nodig heeft (database, hooks die echt vuren, templates) valt
 * buiten deze tests.
 */

date_default_timezone_set( 'Europe/Amsterdam' );

define( 'ABSPATH', __DIR__ . '/' );
define( 'EBOH_THEME_DIR', dirname( __DIR__ ) );

// --- Hook-registratie: alleen onthouden, niet uitvoeren ------------------
$GLOBALS['eboh_test_hooks']      = array();
$GLOBALS['eboh_test_shortcodes'] = array();

function add_action( $hook, $cb = null, $prio = 10, $args = 1 ) {
	$GLOBALS['eboh_test_hooks'][] = array( 'action', $hook, $cb );
}
function add_filter( $hook, $cb = null, $prio = 10, $args = 1 ) {
	$GLOBALS['eboh_test_hooks'][] = array( 'filter', $hook, $cb );
}
function remove_filter() {}
function add_shortcode( $tag, $cb ) {
	$GLOBALS['eboh_test_shortcodes'][ $tag ] = $cb;
}
function apply_filters( $hook, $value ) {
	return $value;
}

// --- Thema-paden ------------------------------------------------------------
function get_template_directory() {
	return EBOH_THEME_DIR;
}
function get_template_directory_uri() {
	return 'https://example.test/wp-content/themes/eboh-theme-v2';
}

// --- Vertaal- en escape-functies (identiteit is genoeg voor deze tests) -----
function __( $text ) { return $text; }
function _e( $text ) { echo $text; }
function esc_html__( $text ) { return $text; }
function esc_attr__( $text ) { return $text; }
function esc_html_e( $text ) { echo $text; }
function esc_attr_e( $text ) { echo $text; }
function esc_html( $text ) { return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' ); }
function esc_attr( $text ) { return esc_html( $text ); }
function esc_url( $url ) { return $url; }
function absint( $n ) { return abs( (int) $n ); }

// --- Customizer-stub die registraties opvangt -------------------------------
class WP_Customize_Control {
	public function __construct( $manager = null, $id = '', $args = array() ) {}
}
class WP_Customize_Media_Control extends WP_Customize_Control {}
class WP_Customize_Image_Control extends WP_Customize_Control {}
class WP_Customize_Color_Control extends WP_Customize_Control {}

class WP_Widget {
	public function __construct( $id = '', $name = '', $opts = array() ) {}
}

class EBOH_Test_Customize {
	public $settings = array();
	public $sections = array();
	public $panels   = array();
	public function add_panel( $id, $args = array() ) { $this->panels[] = $id; }
	public function add_section( $id, $args = array() ) { $this->sections[] = $id; }
	public function add_setting( $id, $args = array() ) { $this->settings[] = $id; }
	public function add_control( $id, $args = array() ) {}
	public function get_setting( $id ) { return null; }
}

// --- Het thema zelf ----------------------------------------------------------
require_once EBOH_THEME_DIR . '/functions.php';

// --- Mini-assertiebibliotheek ------------------------------------------------
$GLOBALS['eboh_test_failures'] = array();
$GLOBALS['eboh_test_count']    = 0;

function eboh_assert_same( $expected, $actual, $message ) {
	$GLOBALS['eboh_test_count']++;
	if ( $expected !== $actual ) {
		$GLOBALS['eboh_test_failures'][] = $message
			. "\n      verwacht: " . var_export( $expected, true )
			. "\n      kreeg:    " . var_export( $actual, true );
	}
}
function eboh_assert_true( $actual, $message ) {
	eboh_assert_same( true, (bool) $actual, $message );
}
