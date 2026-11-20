<?php
// Teamvolgorde: senioren, jongens-jeugd oud->jong, meisjes-jeugd oud->jong, rest alfabetisch.

function eboh_test_sort( array $titles ) {
	usort( $titles, function ( $a, $b ) {
		$ka = eboh_team_sort_key( $a );
		$kb = eboh_team_sort_key( $b );
		for ( $i = 0; $i < count( $ka ); $i++ ) {
			if ( $ka[ $i ] === $kb[ $i ] ) { continue; }
			if ( is_numeric( $ka[ $i ] ) && is_numeric( $kb[ $i ] ) ) {
				return $ka[ $i ] <=> $kb[ $i ];
			}
			return strcmp( (string) $ka[ $i ], (string) $kb[ $i ] );
		}
		return 0;
	} );
	return $titles;
}

eboh_assert_same(
	array( 'EBOH 1', 'EBOH 2', 'EBOH 10', 'EBOH JO19-1', 'EBOH JO17-1', 'EBOH JO17-2', 'EBOH JO9-1', 'EBOH MO17-1', 'EBOH MO13-1', 'Zaalvoetbal' ),
	eboh_test_sort( array( 'Zaalvoetbal', 'EBOH MO13-1', 'EBOH JO9-1', 'EBOH 10', 'EBOH JO17-2', 'EBOH 2', 'EBOH MO17-1', 'EBOH JO19-1', 'EBOH 1', 'EBOH JO17-1' ) ),
	'teamvolgorde: senioren numeriek, daarna JO oud->jong, dan MO oud->jong, onbekend achteraan'
);

eboh_assert_same( array( 0, 12, 0 ), eboh_team_sort_key( 'eboh 12' ), 'senioren: hoofdletterongevoelig, 12 komt na 2 (numeriek, niet tekstueel)' );
eboh_assert_same( array( 1, -19, 1 ), eboh_team_sort_key( 'EBOH JO19' ), 'jeugd zonder volgnummer krijgt volgnummer 1' );
eboh_assert_same( array( 1, -17, 2 ), eboh_team_sort_key( ' EBOH JO17-2 ' ), 'witruimte rond de naam wordt genegeerd' );
eboh_assert_same( 3, eboh_team_sort_key( 'Onbekend team' )[0], 'onbekend formaat valt in groep 3' );
