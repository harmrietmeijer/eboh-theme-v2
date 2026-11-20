#!/usr/bin/env bash
# Verify-commando voor eboh-theme-v2. Stopt bij de eerste fout.
# Gebruik: scripts/verify.sh
set -euo pipefail

cd "$(dirname "$0")/.."

# Kies een PHP die echt start. `command -v php` is geen bewijs: op de
# ontwikkelmachine staat een kapotte x86-binary in /usr/local/bin die eerst
# in PATH komt ("Bad CPU type in executable").
if [ -z "${PHP_BIN:-}" ]; then
	for candidate in php /opt/homebrew/bin/php /usr/bin/php; do
		if "$candidate" -r 'exit(0);' > /dev/null 2>&1; then
			PHP_BIN="$candidate"
			break
		fi
	done
fi
if [ -z "${PHP_BIN:-}" ] || ! "$PHP_BIN" -r 'exit(0);' > /dev/null 2>&1; then
	echo "FOUT: geen werkende PHP gevonden. Zet PHP_BIN=/pad/naar/php"
	exit 1
fi

echo "== PHP: $("$PHP_BIN" -r 'echo PHP_VERSION;') ($PHP_BIN)"

echo "== 1/5 PHP-syntax (php -l)"
count=0
while IFS= read -r -d '' f; do
	"$PHP_BIN" -l "$f" > /dev/null || { echo "FOUT: syntaxfout in $f"; "$PHP_BIN" -l "$f"; exit 1; }
	count=$((count + 1))
done < <(find . -name '*.php' -not -path './.git/*' -not -path './node_modules/*' -not -path './vendor/*' -print0)
echo "   $count bestanden OK"

echo "== 2/5 JavaScript-syntax (node --check)"
if command -v node > /dev/null 2>&1; then
	while IFS= read -r -d '' f; do
		node --check "$f" || { echo "FOUT: syntaxfout in $f"; exit 1; }
	done < <(find assets -name '*.js' -print0)
	echo "   OK"
else
	echo "   OVERGESLAGEN: node niet gevonden"
fi

echo "== 3/5 require_once in functions.php wijst naar bestaande bestanden"
while IFS= read -r rel; do
	[ -f "$rel" ] || { echo "FOUT: functions.php verwijst naar ontbrekend bestand: $rel"; exit 1; }
done < <(grep -oE "get_template_directory\(\) \. '/[^']+\.php'" functions.php | sed -E "s/.*'\/([^']+)'/\1/")
echo "   OK"

echo "== 4/5 thema-header en assets"
grep -qE '^Version: [0-9]+\.[0-9]+\.[0-9]+' style.css || { echo "FOUT: geen geldige 'Version:' in style.css"; exit 1; }
grep -qE '^Text Domain: eboh-v2' style.css || { echo "FOUT: Text Domain in style.css is niet eboh-v2"; exit 1; }
for f in assets/css/eboh-responsive.css assets/js/eboh-scripts.js screenshot.png; do
	[ -f "$f" ] || { echo "FOUT: ontbrekend bestand: $f"; exit 1; }
done
echo "   OK"

echo "== 5/5 tests (tests/run.php)"
"$PHP_BIN" tests/run.php || { echo "FOUT: tests mislukt"; exit 1; }

echo
echo "verify: alles groen"
