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
| `demo/` | Statische HTML-mockups uit de ontwerpfase. Geen onderdeel van het thema. **Niet meeuploaden naar de server.** |

## Commando's

Er is nog **geen** verify-commando, geen tests en geen CI (komt in fase 2/3). Wat nu kan:

```
php -l <bestand.php>                                   # syntaxcheck (lokaal PHP 8.5)
for f in $(find . -name '*.php' -not -path './.git/*'); do php -l "$f"; done
```

`composer` werkt lokaal niet (`Bad CPU type in executable`); `phpcs` is niet geïnstalleerd.

## Flows waar het om draait

| Flow | Waar | Let op |
|---|---|---|
| **Sportlink-data** (programma, uitslagen, stand, teams) | `EBOH_Sportlink_API` in `inc/sportlink-api.php` | `client_id` zit in de optie `eboh_sportlink_client_id` (admin-pagina "Sportlink API"), niet in code. Resultaten gaan als transient `eboh_sl_*` in de DB, TTL minimaal 5 min, standaard 30. Cache wissen = `eboh_sportlink_clear_cache()` (raw `DELETE` op `wp_options`). |
| **Inschrijfformulier** (lid worden) | `eboh_handle_membership_form()` in `functions.php`, actie `admin_post(_nopriv)_eboh_membership_signup` | Mailt o.a. IBAN, geboortedatum en ouder-gegevens in **platte tekst** via `wp_mail`. Ontvanger = theme_mod `eboh_membership_email`, maar die heeft **geen Customizer-control**: in de praktijk gaat het naar `admin_email`. |
| **Nieuws laden (AJAX)** | `eboh_load_more_news()`, nonce `eboh_nonce`, data via `ebohData` | Script wordt met `defer` geladen. |
| **Teamarchief-volgorde** | `eboh_sort_team_archive()` + `eboh_team_sort_key()` | Sorteert in PHP na de query op teamnaam (`EBOH 1`, `EBOH JO17-1`, `EBOH MO13-2`). Een afwijkende naamgeving belandt achteraan. Paginering staat uit voor dit archief. |
| **Standaardpagina's** | `eboh_ensure_default_pages()` op `admin_init` | Maakt `over`, `privacybeleid`, `gebruiksvoorwaarden`, `cookiebeleid`, `organisatie` aan. Overschrijft bestaande pagina's alleen als ze nog letterlijk de v1-placeholder bevatten. Bij nieuwe content: bump `$version` (nu `'v3'`), anders draait het nooit opnieuw. |

## Wat je niet uit de code afleest

- **Twee Sportlink-generaties naast elkaar.** `[eboh_knvb_*]`-shortcodes zijn compat-aliassen die doorverwijzen naar `[eboh_programma]` enz. Bestaande pagina's gebruiken ze nog; verwijder ze niet zonder de content te controleren. Oude `eboh_knvb_*`-transients worden bij cache wissen ook opgeruimd.
- **Twee text domains.** Zowel `'eboh'` (~249×) als `'eboh-v2'` (~110×) komt voor, en de thema-header zegt `eboh-v2`. Gebruik bij nieuwe code `eboh-v2`; hernoem bestaande niet zonder vertaalcontrole.
- **Het versienummer** staat in `style.css` (`Version: 3.0.0`) en wordt door `wp_get_theme()` als cache-buster voor CSS/JS gebruikt. Bump bij elke CSS/JS-wijziging, anders zien bezoekers oude bestanden.
- **Vertaalbestanden** zijn er niet (`Domain Path: /languages` bestaat niet als map).
- **Demo-afbeeldingen** staan dubbel (`demo/images/` en `assets/images/`). `assets/images/` is leidend.

## Bekende afwijkingen en problemen

| # | Wat | Waar |
|---|---|---|
| 1 | Sportlink-instellingen zijn bewerkbaar voor iedereen met `edit_posts` (auteurs/redacteuren), niet alleen beheerders | `inc/sportlink-api.php`, `eboh_sportlink_admin_menu()` en `register_settings()` |
| 2 | Sponsors: `[eboh_sponsor_grid]` toont **zes hardcoded placeholder-afbeeldingen**; er is geen sponsor-CPT, ondanks wat README beweert | `inc/shortcodes.php` |
| 3 | `$_POST['first_name']`, `['last_name']`, `['email']` worden in onderwerp/headers zonder `wp_unslash` gebruikt | `functions.php`, `eboh_handle_membership_form()` |
| 4 | `README.md` noemt `inc/customizer.php`; dat bestand bestaat niet (Customizer zit in `functions.php`) | docs |
| 5 | `DEPLOYMENT.md` / `THEME-STRUCTURE.txt` beschrijven "EBOH Enfold Child" 1.0.0 en vereisen Enfold en Contact Form 7; beide zijn niet meer waar | docs |
| 6 | Placeholder `i4LQjMjLoJ` in het Sportlink-veld lijkt op een echt client-ID — controleren | `inc/sportlink-api.php` |

Nog niet opgelost; er zijn ook nog geen tests die ze bewaken.

## Deploy

**Onbekend / nog niet vastgelegd.** In de repo staat geen deployscript. Vul dit aan zodra de eigenaar bevestigt hoe het naar productie gaat (zip via WP-admin, SFTP, SSH) en welke PHP-versie daar draait. Controleer na elke deploy dat de site de nieuwe `Version` laadt (bekijk de `?ver=` van `style.css` in de paginabron), niet alleen dat de upload slaagde.

## Werkwijze

1. Taak als issue, branch vanaf `main` met issuenummer erin. Nooit direct op `main`.
2. Bij meer dan één bestand: eerst lezen, plan schrijven, akkoord afwachten.
3. Kleine stappen, complete bestanden, geen fragmenten.
4. Na elke wijziging: `php -l` op alles wat je aanraakte (tot er een echt verify-commando is).
5. PR met vier kopjes: wat en waarom / welke flows geraakt / hoe getest / wat de mens handmatig moet controleren.
6. Houd dit bestand bij als een conventie of de architectuur verandert.

**Nooit:** `.env` of `wp-config.php` lezen, credentials of API-sleutels in code of docs zetten, productie-DB of live Sportlink-API aanspreken vanuit tests, `demo/` naar productie sturen.
