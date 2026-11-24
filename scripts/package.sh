#!/usr/bin/env bash
# Maakt dist/eboh-theme-v2-<versie>.zip met alleen wat op de server hoort
# (zie .distignore). De zip bevat één map "eboh-theme-v2/".
# Draait eerst scripts/verify.sh en stopt als die faalt.
set -euo pipefail
cd "$(dirname "$0")/.."

./scripts/verify.sh

version="$(sed -nE 's/^Version: *([0-9.]+).*/\1/p' style.css | head -1)"
[ -n "$version" ] || { echo "FOUT: geen Version in style.css"; exit 1; }

stage="$(mktemp -d)"
trap 'rm -rf "$stage"' EXIT
rsync -a --exclude-from=.distignore ./ "$stage/eboh-theme-v2/"

# Sanity: dit mag er nooit in zitten (.env = credentials, nooit uploaden).
if find "$stage/eboh-theme-v2" -name '.env*' | grep -q .; then echo "FOUT: een .env-bestand zit in het pakket"; exit 1; fi
for verboden in demo tests scripts docs .git .github CLAUDE.md deploy.env.example .env; do
	[ ! -e "$stage/eboh-theme-v2/$verboden" ] || { echo "FOUT: $verboden zit in het pakket"; exit 1; }
done
for vereist in style.css functions.php screenshot.png assets/js/eboh-scripts.js; do
	[ -e "$stage/eboh-theme-v2/$vereist" ] || { echo "FOUT: $vereist ontbreekt in het pakket"; exit 1; }
done

mkdir -p dist
out="dist/eboh-theme-v2-$version.zip"
rm -f "$out"
(cd "$stage" && zip -qr "$OLDPWD/$out" eboh-theme-v2)
echo "Pakket: $out ($(du -h "$out" | cut -f1))"
