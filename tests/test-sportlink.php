<?php
// Sportlink-hulpfuncties die zonder WordPress of netwerk te testen zijn.

// --- eboh_sportlink_normalize_match -----------------------------------------
$ts = mktime( 14, 30, 0, 11, 22, 2026 ); // zondag 22 november 2026

$m = eboh_sportlink_normalize_match(
	array(
		'thuisteam'       => ' EBOH 1 ',
		'uitteam'         => 'CKC 1',
		'_ts'             => $ts,
		'aanvangstijd'    => '14:30',
		'competitienaam'  => 'Reserve 4e klasse',
		'accommodatie'    => 'Schenkeldijk',
		'doelpuntenthuis' => 0,
		'doelpuntenuit'   => 3,
	),
	'eboh 1',
	true
);
eboh_assert_same( 'EBOH 1', $m['thuisteam'], 'normalize: teamnaam wordt getrimd' );
eboh_assert_same( 'Zondag 22 november', $m['datum'], 'normalize: Nederlandse datum' );
eboh_assert_same( 'ZONDAG 22 NOV', $m['datum_kort'], 'normalize: korte datum in hoofdletters' );
eboh_assert_same( 0, $m['score_thuis'], 'normalize: een score van 0 blijft 0 (niet null)' );
eboh_assert_same( 3, $m['score_uit'], 'normalize: uitscore' );
eboh_assert_same( true, $m['is_eboh_home'], 'normalize: thuiswedstrijd herkend, hoofdletterongevoelig' );

$m = eboh_sportlink_normalize_match( array( 'thuisteam' => 'CKC 1', 'uitteam' => 'EBOH 1', 'scorethuis' => '2', 'scoreuit' => '1' ), 'EBOH 1' );
eboh_assert_same( '2', $m['score_thuis'], 'normalize: valt terug op veld scorethuis' );
eboh_assert_same( false, $m['is_eboh_home'], 'normalize: uitwedstrijd is geen thuiswedstrijd' );
eboh_assert_same( '', $m['datum_kort'], 'normalize: zonder timestamp is de datum leeg' );

// --- eboh_sportlink_format_date -----------------------------------------------
eboh_assert_same( 'Zo 22 nov', eboh_sportlink_format_date( array( 'wedstrijddatum' => '2026-11-22' ) ), 'format_date: ISO-datum' );
eboh_assert_same( '', eboh_sportlink_format_date( array() ), 'format_date: geen datumveld geeft leeg' );
eboh_assert_same( 'onzin', eboh_sportlink_format_date( array( 'datum' => 'onzin' ) ), 'format_date: onleesbare datum komt ongewijzigd terug' );

// --- eboh_v2_extract_clubnaam -----------------------------------------------
eboh_assert_same( 'EBOH', eboh_v2_extract_clubnaam( 'EBOH JO17-2' ), 'clubnaam: EBOH JO17-2' );
eboh_assert_same( 'EBOH', eboh_v2_extract_clubnaam( 'EBOH 1' ), 'clubnaam: EBOH 1' );
eboh_assert_same( 'Rijsoord', eboh_v2_extract_clubnaam( 'Rijsoord 1' ), 'clubnaam: Rijsoord 1' );
eboh_assert_same( 'SC Heerenveen', eboh_v2_extract_clubnaam( 'SC Heerenveen MO13-1' ), 'clubnaam: meerdere woorden' );

// --- Compat-aliassen ---------------------------------------------------------
foreach ( array( 'programma', 'uitslagen', 'stand', 'teams', 'overzicht' ) as $naam ) {
	eboh_assert_true( isset( $GLOBALS['eboh_test_shortcodes'][ 'eboh_knvb_' . $naam ] ), "shortcode eboh_knvb_$naam bestaat nog (bestaande pagina's gebruiken hem)" );
	eboh_assert_true( isset( $GLOBALS['eboh_test_shortcodes'][ 'eboh_' . $naam ] ), "shortcode eboh_$naam bestaat" );
}
