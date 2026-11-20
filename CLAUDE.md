# CLAUDE.md — eboh-theme-v2

Standalone WordPress-thema voor vv EBOH (Dordrecht). Donker thema (zwart/wit/rood), géén parent theme, geen page builder. Taal van code-commentaar, docs en UI: Nederlands.

**Waar de waarheid staat:** de code. `README.md`, `DEPLOYMENT.md`, `SETUP.md` en `THEME-STRUCTURE.txt` komen deels uit v1 (Enfold child theme, versie 1.0.0) en zijn niet betrouwbaar. Zie "Bekende afwijkingen" hieronder.

## Structuur

| Pad | Waarvoor |
|---|---|
| `style.css` | Thema-header (versie!) + alle hoofd-CSS |
| `functions.php` | Setup, enqueue, Customizer, AJAX nieuws, inschrijfformulier, standaardpagina's, teamvolgorde |
| `inc/` | CPT `team`, shortcodes, widgets, Sportlink-koppeling |
| `template-*.php`, `front-page.php`, `single-team.php` | Pagina-templates |
| `parts/news-card.php` | Herbruikbare nieuwskaart |
| `assets/` | `css/eboh-responsive.css`, `js/eboh-scripts.js`, afbeeldingen |
| `demo/` | Statische HTML-mockups uit de ontwerpfase. Geen onderdeel van het thema. **Niet meeuploaden naar de server**: `.distignore` sluit het uit en `package.sh` controleert dat. |

## Commando's

```
scripts/verify.sh        # DE feedbackloop: draai na elke wijziging, stopt bij de eerste fout
scripts/package.sh       # draait verify, bouwt dist/eboh-theme-v2-<versie>.zip volgens .distignore
scripts/deploy.sh        # DROOGLOOP naar de server (FTPS/SFTP via lftp), uploadt niets
scripts/deploy.sh --go   # uploadt echt, daarna controle op de live site
```

Het script controleert: PHP-syntax van alle `.php`, JS-syntax (`node --check`), dat elke `require_once` in `functions.php` naar een bestaand bestand wijst, en dat de thema-header (`Version`, `Text Domain: eboh-v2`) en de basisassets kloppen. CI (`.github/workflows/verify.yml`) draait hetzelfde script op PHP 7.4 en 8.3. Daarna draait `tests/run.php`: een kleine eigen assert-runner (geen PHPUnit, geen dependencies) die het thema laadt via `tests/bootstrap.php` met stubs in plaats van WordPress. Getest wordt alleen wat zonder WordPress, database of netwerk kan: `eboh_team_sort_key`, `eboh_sportlink_normalize_match`, `eboh_sportlink_format_date`, `eboh_v2_extract_clubnaam`, de compat-shortcodes en de Customizer-registratie. Alles met `$wpdb`, echte hooks of templates is niet gedekt. Nieuwe test = nieuw bestand `tests/test-*.php`. Er is nog geen statische analyse.

**Machine-eigenaardigheid:** `/usr/local/bin/php` is een kapotte x86-binary (`Bad CPU type in executable`) die in `PATH` vóór de werkende `/opt/homebrew/bin/php` (8.5.12) staat. Dat is ook waarom `composer` lokaal niet start. `verify.sh` kiest zelf een werkende PHP; of zet `PHP_BIN=/opt/homebrew/bin/php`. Lokaal is PHP 7.4 (de ondergrens) niet te testen; dat gebeurt alleen in CI.

## Flows waar het om draait

| Flow | Waar | Let op |
|---|---|---|
| **Sportlink-data** (programma, uitslagen, stand, teams) | `EBOH_Sportlink_API` in `inc/sportlink-api.php` | `client_id` zit in de optie `eboh_sportlink_client_id` (admin-pagina "Sportlink API"), niet in code. Resultaten gaan als transient `eboh_sl_*` in de DB, TTL minimaal 5 min, standaard 30. Cache wissen = `eboh_sportlink_clear_cache()` (raw `DELETE` op `wp_options`). |
| **Inschrijfformulier** (lid worden) | `eboh_handle_membership_form()` in `functions.php`, actie `admin_post(_nopriv)_eboh_membership_signup` | Mailt o.a. IBAN, geboortedatum en ouder-gegevens in **platte tekst** via `wp_mail`. Ontvanger = theme_mod `eboh_membership_email`, maar die heeft **geen Customizer-control**: in de praktijk gaat het naar `admin_email`. |
| **Nieuws laden (AJAX)** | `eboh_load_more_news()`, nonce `eboh_nonce`, data via `ebohData` | Script wordt met `defer` geladen. |
| **Teamarchief-volgorde** | `eboh_sort_team_archive()` + `eboh_team_sort_key()` | Sorteert in PHP na de query op teamnaam (`EBOH 1`, `EBOH JO17-1`, `EBOH MO13-2`). Een afwijkende naamgeving belandt achteraan. Paginering staat uit voor dit archief. |
| **Standaardpagina's** | `eboh_ensure_default_pages()` op `admin_init` | Maakt `over`, `privacybeleid`, `gebruiksvoorwaarden`, `cookiebeleid`, `organisatie` aan. Overschrijft bestaande pagina's alleen als ze nog letterlijk de v1-placeholder bevatten. Bij nieuwe content: bump `$version` (nu `'v3'`), anders draait het nooit opnieuw. |

## Wat je niet uit de code afleest

- **Docs:** `DEPLOYMENT.md` en `THEME-STRUCTURE.txt` (v1/Enfold) zijn verwijderd; README en SETUP zijn gecorrigeerd. Staat er in de git-history nog iets van Enfold, dan is dat oud.
- **Ongebruikt maar niet verwijderd:** functies `eboh_get_club_info`, `eboh_get_social_links`, `eboh_get_next_home_match`, `eboh_get_team_stats` hebben geen aanroeper in de code; en 29 afbeeldingen in `assets/images/` worden nergens bij naam genoemd (kunnen dynamisch geladen worden). Eerst controleren op de live site voordat je ze weghaalt.
- **Twee Sportlink-generaties naast elkaar.** `[eboh_knvb_*]`-shortcodes zijn compat-aliassen die doorverwijzen naar `[eboh_programma]` enz. Bestaande pagina's gebruiken ze nog; verwijder ze niet zonder de content te controleren. Oude `eboh_knvb_*`-transients worden bij cache wissen ook opgeruimd.
- **Twee text domains.** Zowel `'eboh'` (~249×) als `'eboh-v2'` (~110×) komt voor, en de thema-header zegt `eboh-v2`. Gebruik bij nieuwe code `eboh-v2`; hernoem bestaande niet zonder vertaalcontrole.
- **Het versienummer** staat in `style.css` (`Version: 3.0.0`) en wordt door `wp_get_theme()` als cache-buster voor CSS/JS gebruikt. Bump bij elke CSS/JS-wijziging, anders zien bezoekers oude bestanden.
- **Vertaalbestanden** zijn er niet (`Domain Path: /languages` bestaat niet als map).
- **Demo-afbeeldingen** staan dubbel (`demo/images/` en `assets/images/`). `assets/images/` is leidend.

## Bekende afwijkingen en problemen

| # | Wat | Waar |
|---|---|---|
| 1 | Sportlink-instellingen zijn bewerkbaar voor iedereen met `edit_posts` (auteurs/redacteuren), niet alleen beheerders | `inc/sportlink-api.php`, `eboh_sportlink_admin_menu()` en `register_settings()` |
| 2 | Sponsors: `[eboh_sponsor_grid]` toont **zes hardcoded placeholder-afbeeldingen**; er is geen sponsor-CPT | `inc/shortcodes.php` |
| 3 | `$_POST['first_name']`, `['last_name']`, `['email']` worden in onderwerp/headers zonder `wp_unslash` gebruikt | `functions.php`, `eboh_handle_membership_form()` |
| 7 | Elf `get_theme_mod()`-sleutels hebben geen Customizer-setting (community-blok, `eboh_cta_subtext`, social-links, sponsor-titels, `eboh_membership_email`): in de praktijk wint altijd de hardcoded fallback in `front-page.php`/`footer.php`. | lijst in `tests/test-customizer.php` |
| 6 | Placeholder `i4LQjMjLoJ` in het Sportlink-veld lijkt op een echt client-ID — controleren | `inc/sportlink-api.php` |

Nog niet opgelost. Alleen #7 wordt bewaakt: `tests/test-customizer.php` faalt zodra er een twaalfde sleutel bijkomt, of zodra er een setting bij komt zonder dat je hem uit de lijst haalt.

## Deploy

`scripts/deploy.sh` (droogloop) en `scripts/deploy.sh --go` (echt). Draait eerst `verify.sh` en `package.sh`, uploadt via `lftp` (FTPS standaard, `FTP_PROTO=sftp` kan ook) en **verwijdert nooit iets** op de server.

| Wat | Hoe |
|---|---|
| Credentials | Alleen in `.env` (kopieer `deploy.env.example`; staat in `.gitignore`). Het script leest het; Claude leest en tikt nooit wachtwoorden. Nooit `.env` of `FTP_PASS` in chat, logs of code. |
| Alleen vanaf `main` | Schone werkmap, gelijk met `origin/main`. Anders weigert het script (`DEPLOY_ANY_BRANCH=1` om te forceren, alleen op uitdrukkelijk verzoek). |
| Vreemde bestanden op de server | Droogloop en `--go` tonen wat er op de server staat maar niet in het pakket zit (bv. een oude `demo/`). Ze worden niet verwijderd; opruimen doet de eigenaar. |
| Controle na upload | Met `SITE_URL` in `.env`: de site moet `style.css?ver=<Version>` laden, en `demo/`, `tests/`, `CLAUDE.md` en `.env` mogen niet met HTTP 200 bereikbaar zijn. Bij afwijking: exit 1, niet als gelukt beschouwen (denk aan een cache-plugin of CDN). Zonder `SITE_URL` meldt het script dat er niet gecontroleerd is. |
| Certificaten | `ssl:verify-certificate yes`. Faalt FTPS op het certificaat, los dat bij de host op; zet verificatie niet uit. |

Claude draait `--go` alleen na uitdrukkelijk akkoord van de eigenaar in die sessie; het commando staat op `ask` in `.claude/settings.json`. Hoe de PHP-versie op de server is ingesteld is nog onbekend.

## Werkwijze

1. Taak als issue, branch vanaf `main` met issuenummer erin. Nooit direct op `main`.
2. Bij meer dan één bestand: eerst lezen, plan schrijven, akkoord afwachten.
3. Kleine stappen, complete bestanden, geen fragmenten.
4. Na elke wijziging: `scripts/verify.sh`. Pas klaar als alles groen is.
5. PR met vier kopjes: wat en waarom / welke flows geraakt / hoe getest / wat de mens handmatig moet controleren.
6. Definition of done: verify groen, tests voor nieuwe of gewijzigde logica, CLAUDE.md of `docs/decisions/` bijgewerkt bij een nieuwe conventie, en na een deploy gecontroleerd dat de site de nieuwe `?ver=` laadt.
7. `Fixes #N` sluit alleen bij een merge naar de default branch (`main`).

**Nooit:** `.env` of `wp-config.php` lezen, credentials of API-sleutels in code of docs zetten, productie-DB of live Sportlink-API aanspreken vanuit tests, `demo/` naar productie sturen.
