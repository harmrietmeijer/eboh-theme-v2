#!/usr/bin/env bash
# Deploy eboh-theme-v2 naar de server via FTPS of SFTP (lftp).
#
#   scripts/deploy.sh            # DROOGLOOP: toont wat er geüpload zou worden
#   scripts/deploy.sh --go       # uploadt echt
#
# Credentials komen uit .env (zie deploy.env.example); dat bestand staat in
# .gitignore en wordt alleen door dit script gelezen. Het wachtwoord wordt niet
# getoond en staat niet in de argumenten van een proces.
#
# Er wordt NOOIT iets op de server verwijderd. Bestanden die er al staan maar
# niet in het pakket zitten, worden alleen gemeld.
set -euo pipefail
cd "$(dirname "$0")/.."

GO=0
[ "${1:-}" = "--go" ] && GO=1

ENV_FILE="${ENV_FILE:-.env}"
[ -f "$ENV_FILE" ] || { echo "FOUT: $ENV_FILE ontbreekt. Kopieer deploy.env.example naar .env en vul het in."; exit 1; }
set -a
# shellcheck disable=SC1090
. "$ENV_FILE"
set +a

FTP_PROTO="${FTP_PROTO:-ftps}"
for v in FTP_PATH; do
	[ -n "${!v:-}" ] || { echo "FOUT: $v ontbreekt in $ENV_FILE"; exit 1; }
done
if [ "$FTP_PROTO" != "file" ]; then
	for v in FTP_HOST FTP_USER FTP_PASS; do
		[ -n "${!v:-}" ] || { echo "FOUT: $v ontbreekt in $ENV_FILE"; exit 1; }
	done
fi
case "$FTP_PROTO" in ftps|sftp|file) ;; *) echo "FOUT: FTP_PROTO moet ftps, sftp of file zijn"; exit 1 ;; esac
command -v lftp > /dev/null || { echo "FOUT: lftp ontbreekt (brew install lftp)"; exit 1; }

# --- Vangrails op de repo ----------------------------------------------------
if [ "${DEPLOY_ANY_BRANCH:-0}" != "1" ]; then
	branch="$(git rev-parse --abbrev-ref HEAD)"
	[ "$branch" = "main" ] || { echo "FOUT: je staat op '$branch'. Deploy alleen vanaf main (of zet DEPLOY_ANY_BRANCH=1)."; exit 1; }
	[ -z "$(git status --porcelain)" ] || { echo "FOUT: er zijn niet-gecommitte wijzigingen."; exit 1; }
	git fetch -q origin main
	[ "$(git rev-parse HEAD)" = "$(git rev-parse origin/main)" ] || { echo "FOUT: main loopt niet gelijk met origin/main (pull of push eerst)."; exit 1; }
fi

# --- Pakket bouwen (draait verify.sh) ----------------------------------------
./scripts/package.sh
version="$(sed -nE 's/^Version: *([0-9.]+).*/\1/p' style.css | head -1)"
zip="dist/eboh-theme-v2-$version.zip"
stage="$(mktemp -d)"
trap 'rm -rf "$stage"' EXIT
unzip -q "$zip" -d "$stage"
src="$stage/eboh-theme-v2"

# --- lftp-opdrachten (via stdin, dus niet zichtbaar in `ps`) -----------------
q() { printf '%s' "$1" | sed -e 's/\\/\\\\/g' -e 's/"/\\"/g'; }

lftp_run() {
	{
		echo "set cmd:fail-exit yes"
		echo "set net:max-retries 2"
		echo "set net:timeout 20"
		case "$FTP_PROTO" in
			ftps)
				echo "set ftp:ssl-force true"
				echo "set ftp:ssl-protect-data true"
				echo "set ssl:verify-certificate yes"
				echo "open -u \"$(q "$FTP_USER")\",\"$(q "$FTP_PASS")\" ${FTP_PORT:+-p $FTP_PORT} ftp://$FTP_HOST" ;;
			sftp)
				echo "set sftp:auto-confirm no"
				echo "open -u \"$(q "$FTP_USER")\",\"$(q "$FTP_PASS")\" ${FTP_PORT:+-p $FTP_PORT} sftp://$FTP_HOST" ;;
			file)
				echo "open file://$FTP_PATH" ;;
		esac
		cat
	}  | lftp
}

remote_dir="$FTP_PATH"
[ "$FTP_PROTO" = "file" ] && remote_dir="."

echo "== Verbinding testen ($FTP_PROTO)"
printf 'cd "%s"\ncls -1\n' "$(q "$remote_dir")" | lftp_run > /dev/null \
	|| { echo "FOUT: kon niet verbinden of het pad bestaat niet (FTP_PATH)."; exit 1; }
echo "   OK"

echo "== Bestaat er op de server iets dat niet in het pakket zit? (wordt NIET verwijderd)"
extra="$(printf 'cd "%s"\ncls -1\n' "$(q "$remote_dir")" | lftp_run 2>/dev/null | sed 's|^\./||; s|/$||' | sort -u || true)"
mine="$(cd "$src" && find . -mindepth 1 -maxdepth 1 | sed 's|^\./||' | sort -u)"
vreemd="$(comm -23 <(printf '%s\n' "$extra" | grep -v '^$' | sed 's|/.*||' | sort -u) <(printf '%s\n' "$mine"))"
if [ -n "$vreemd" ]; then
	echo "   Alleen op de server (let op, mogelijk oude rommel):"
	printf '%s\n' "$vreemd" | sed 's/^/     - /'
else
	echo "   niets"
fi

mirror_opts="-R --verbose --parallel=2"
if [ "$GO" -ne 1 ]; then
	echo "== DROOGLOOP (er wordt niets geüpload; gebruik --go om echt te deployen)"
	printf 'mirror %s --dry-run "%s" "%s"\n' "$mirror_opts" "$(q "$src")" "$(q "$remote_dir")" | lftp_run
	echo
	echo "Droogloop klaar. Niets is gewijzigd op de server."
	exit 0
fi

echo "== UPLOAD v$version (zonder verwijderen)"
printf 'mirror %s "%s" "%s"\n' "$mirror_opts" "$(q "$src")" "$(q "$remote_dir")" | lftp_run

# --- Controle: staat het er écht? ---------------------------------------------
if [ -z "${SITE_URL:-}" ]; then
	echo
	echo "Upload klaar, maar SITE_URL staat niet in $ENV_FILE: de nieuwe versie is NIET gecontroleerd."
	exit 0
fi

echo "== Controle op $SITE_URL"
html="$(curl -fsSL -H 'Cache-Control: no-cache' "$SITE_URL/?nocache=$(date +%s)" || true)"
live="$(printf '%s' "$html" | grep -oE "eboh-style-css'[^>]*href='[^']*style\.css\?ver=[0-9.]+" | head -1 | sed -E 's/.*ver=//')"
if [ "$live" = "$version" ]; then
	echo "   OK: de site laadt versie $version"
else
	echo "   FOUT: de site laadt versie '${live:-onbekend}', verwacht $version."
	echo "   Mogelijk een cache (plugin/CDN) of het thema is niet actief. NIET als gelukt beschouwen."
	exit 1
fi

base="$(printf '%s' "$html" | grep -oE "eboh-style-css'[^>]*href='[^']*style\.css" | head -1 | sed -E "s/.*href='//; s|/style\.css$||")"
if [ -n "$base" ]; then
	for pad in demo/index.html tests/run.php CLAUDE.md .env; do
		code="$(curl -s -o /dev/null -w '%{http_code}' "$base/$pad" || echo 000)"
		if [ "$code" = "200" ]; then
			echo "   WAARSCHUWING: $base/$pad is publiek leesbaar (HTTP 200). Verwijder het van de server."
			warn=1
		fi
	done
	[ "${warn:-0}" = "1" ] && exit 1
	echo "   OK: demo/, tests en .env zijn niet publiek bereikbaar"
fi
echo
echo "deploy: klaar en gecontroleerd"
