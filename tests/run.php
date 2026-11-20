<?php
/**
 * Draait alle tests/test-*.php. Exit 1 bij een mislukte assertie.
 * Gebruik: php tests/run.php
 */
require __DIR__ . '/bootstrap.php';

foreach ( glob( __DIR__ . '/test-*.php' ) as $file ) {
	echo '   ' . basename( $file ) . "\n";
	require $file;
}

$failures = $GLOBALS['eboh_test_failures'];
echo '   ' . $GLOBALS['eboh_test_count'] . ' asserties, ' . count( $failures ) . " mislukt\n";
if ( $failures ) {
	foreach ( $failures as $f ) {
		echo "   FOUT: $f\n";
	}
	exit( 1 );
}
