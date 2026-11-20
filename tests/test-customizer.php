<?php
// Elke get_theme_mod( 'x' ) in het thema hoort een Customizer-setting te hebben.
// Zonder setting kan niemand de waarde invullen en werkt de code altijd met
// de fallback (zo ging het mis met 'eboh_membership_email').

$customize = new EBOH_Test_Customize();
eboh_customize_register( $customize );
$registered = array_flip( $customize->settings );

// Bewust nog open: zie "Bekende afwijkingen" in CLAUDE.md. Haal een regel hier
// weg zodra de bijbehorende setting bestaat; de test dwingt dat af.
$known_missing = array(
	'eboh_community_intro',
	'eboh_community_link',
	'eboh_community_link_text',
	'eboh_community_title',
	'eboh_cta_subtext',
	'eboh_membership_email',
	'eboh_social_facebook',
	'eboh_social_instagram',
	'eboh_social_x',
	'eboh_sponsors_title_part1',
	'eboh_sponsors_title_part2',
);

// WordPress zelf levert deze:
$core = array( 'custom_logo' );

$used = array();
$it   = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( EBOH_THEME_DIR, FilesystemIterator::SKIP_DOTS ) );
foreach ( $it as $file ) {
	$path = $file->getPathname();
	if ( substr( $path, -4 ) !== '.php' || strpos( $path, '/.git/' ) !== false || strpos( $path, '/tests/' ) !== false ) {
		continue;
	}
	if ( preg_match_all( "/get_theme_mod\(\s*'([a-z0-9_]+)'\s*[,)]/", file_get_contents( $path ), $m ) ) {
		$used = array_merge( $used, $m[1] );
	}
}
$used = array_values( array_diff( array_unique( $used ), $core ) );

eboh_assert_true( count( $used ) > 10, 'customizer: er zijn theme_mods gevonden om te controleren (' . count( $used ) . ')' );

$missing = array();
foreach ( $used as $key ) {
	if ( ! isset( $registered[ $key ] ) ) {
		$missing[] = $key;
	}
}
sort( $missing );
sort( $known_missing );
eboh_assert_same( $known_missing, $missing, 'customizer: theme_mods zonder setting komen overeen met de bekende lijst' );
