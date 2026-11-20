# 001. Eigen testrunner zonder dependencies

Status: geaccepteerd
Datum: 2026-11-20

## Context
Het thema had geen tests. Lokaal start `composer` niet (`/usr/local/bin/php` is een kapotte x86-binary die eerst in PATH staat), dus PHPUnit installeren was niet zonder omweg mogelijk. Het thema is klein en de te testen logica bestaat vooral uit pure functies (teamvolgorde, Sportlink-normalisatie, datumopmaak).

## Besluit
`tests/run.php` is een eigen assert-runner, zonder PHPUnit of WordPress-testsuite. `tests/bootstrap.php` laadt het thema met minimale stubs. Tests staan in `tests/test-*.php` en draaien als stap 5 van `scripts/verify.sh`, lokaal en in CI.

## Gevolgen
- Geen dependencies, geen download, draait op PHP 7.4 tot 8.x.
- Alleen wat zonder WordPress, database of netwerk kan is te testen. Templates, echte hooks en `$wpdb`-code zijn niet gedekt.
- Elke nieuwe WordPress-functie die code bij het laden aanroept moet in `tests/bootstrap.php` gestubd worden.
- Er is geen mocking, data providers of coverage-rapport.

## Afgewogen alternatieven
- **PHPUnit via composer:** meer functionaliteit, maar composer werkt lokaal niet en het voegt een vendor-map toe aan een thema zonder composer.json.
- **WordPress-testsuite (WP_UnitTestCase):** dekt meer, maar vraagt een testdatabase en een aparte installatie; nu niet in verhouding.

Wil je later toch PHPUnit, schrijf dan een nieuwe ADR die deze vervangt; houd de bestaande `tests/test-*.php`-asserties als uitgangspunt.
