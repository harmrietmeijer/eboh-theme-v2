<?php
// Hulpfuncties voor de donkere homepage-widget.

// --- eboh_v2_team_abbr -------------------------------------------------------
$abbr = array(
	'EBOH 1'             => 'EBO',
	'EBOH JO17-2'        => 'EBO',
	'Spirit 1'           => 'SPI',
	'vv DESK 1'          => 'DES',
	'v.v. Spirit 1'      => 'SPI',
	'FC Binnenmaas 1'    => 'BIN',
	'Nieuw-Lekkerland 1' => 'NIE',
	'GJS 1'              => 'GJS',
	'SVW 1'              => 'SVW',
	'Roda Boys 1'        => 'ROD',
	'AB 1'               => 'AB',
);
foreach ( $abbr as $naam => $verwacht ) {
	eboh_assert_same( $verwacht, eboh_v2_team_abbr( $naam ), "afkorting van '$naam'" );
}
eboh_assert_same( '', eboh_v2_team_abbr( '' ), 'afkorting van een lege naam is leeg' );

// --- eboh_v2_format_datum_tijd -----------------------------------------------
$ts = mktime( 14, 45, 0, 11, 21, 2026 ); // zaterdag 21 november 2026
eboh_assert_same( 'ZA 21 NOV - 14:45 UUR', eboh_v2_format_datum_tijd( $ts, '14:45' ), 'datum_tijd met tijd' );
eboh_assert_same( 'ZA 21 NOV', eboh_v2_format_datum_tijd( $ts, '' ), 'datum_tijd zonder tijd' );
eboh_assert_same( '', eboh_v2_format_datum_tijd( 0, '14:45' ), 'datum_tijd zonder timestamp is leeg' );

// --- normalize_match levert de nieuwe velden ---------------------------------
$m = eboh_sportlink_normalize_match( array( 'thuisteam' => 'EBOH 1', 'uitteam' => 'Spirit 1', '_ts' => $ts, 'aanvangstijd' => '14:45', 'poulecode' => '1234' ), 'EBOH 1' );
eboh_assert_same( 'ZA 21 NOV - 14:45 UUR', $m['datum_tijd'], 'normalize: datum_tijd' );
eboh_assert_same( '1234', $m['poulecode'], 'normalize: poulecode doorgegeven' );
$m = eboh_sportlink_normalize_match( array( 'thuisteam' => 'EBOH 1', 'uitteam' => 'Spirit 1' ), 'EBOH 1' );
eboh_assert_same( '', $m['poulecode'], 'normalize: zonder poulecode leeg' );

// --- competitie -----------------------------------------------------------------
eboh_assert_same( 'Mannen Zaterdag Standaard (A-CAT)', eboh_v2_competition_label( '0212 Mannen Zaterdag Standaard (A-CAT)' ), 'competitielabel zonder code' );
eboh_assert_same( '2e klasse F', eboh_v2_competition_label( '2e klasse F' ), 'competitielabel laat gewone naam met rust' );
eboh_assert_same( 'knvb-beker', eboh_v2_competition_icon_basename( 'KNVB Districtsbeker poulefase' ), 'beker-icoon voor bekerwedstrijd' );
eboh_assert_same( 'knvb-competitie', eboh_v2_competition_icon_basename( '2e klasse F' ), 'competitie-icoon voor de rest' );
