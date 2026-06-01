# EBOH Theme — Installatie & Setup

Stap-voor-stap handleiding om het EBOH WordPress thema te installeren, configureren en met content te vullen. Het thema is **standalone** — er is géén parent theme of page builder nodig.

---

## 1. Vereisten

- WordPress 5.8 of hoger
- PHP 7.4 of hoger (PHP 8.1+ aanbevolen)
- MySQL 5.7+ of MariaDB 10.3+
- HTTPS (verplicht voor formulieren en cookies)
- Schrijfrechten op `/wp-content/uploads/`

---

## 2. Theme uploaden

### Via WordPress admin (aanbevolen)

1. Maak een zip van de **map** `eboh-theme/` (zip de map zelf, niet alleen de inhoud).
2. Ga naar **Weergave → Thema's → Nieuw thema toevoegen → Thema uploaden**.
3. Kies de zip en klik **Nu installeren**.
4. Klik **Activeren**.

### Via FTP/SFTP

1. Upload de map `eboh-theme/` naar `/wp-content/themes/`.
2. Ga naar **Weergave → Thema's** en activeer **EBOH**.

---

## 3. Permalinks instellen

Direct na activatie:

1. Ga naar **Instellingen → Permalinks**.
2. Kies **Berichtnaam** (`/%postname%/`).
3. Klik **Wijzigingen opslaan**.

> Dit is essentieel om de Custom Post Types (`team`, `sponsor`) goed door te laten linken.

---

## 4. Menu aanmaken

1. Ga naar **Weergave → Menu's**.
2. Maak een nieuw menu, naam: `Hoofdmenu`.
3. Voeg pagina's toe (zie stap 5 — eerst pagina's aanmaken).
4. Vink onderaan **Primair Menu** aan en klik **Menu opslaan**.

---

## 5. Pagina's aanmaken

Maak de volgende pagina's aan via **Pagina's → Nieuwe pagina** en kies bij **Pagina-attributen → Sjabloon** het juiste template:

| Pagina-titel | Sjabloon |
|---|---|
| Home | Standaard (zet daarna in **Instellingen → Lezen** als statische voorpagina) |
| Nieuws | Nieuws |
| Teams | Teams |
| Programma | Programma & Uitslagen |
| Lid Worden | Lid Worden |
| Sponsoring | Sponsoring |
| Contact | Contact |

Ga daarna naar **Instellingen → Lezen** → **Een statische pagina** → kies *Home* als voorpagina en *Nieuws* als berichtenpagina.

---

## 6. Customizer invullen

Ga naar **Weergave → Aanpassen** en doorloop deze secties:

### Site identiteit
- Upload logo (PNG, transparant, ~300px hoog)
- Site icoon (favicon, 512×512)

### EBOH — Hero
- Hero titel, subtitel
- CTA-knoptekst en link
- Hero achtergrondafbeelding (1920×1080+)

### EBOH — Over de club
- Sectiekop, intro tekst, afbeelding

### EBOH — CTA banner
- Tekst en knop

### EBOH — Parallax sectie
- Afbeelding en quote

### EBOH — Contactgegevens
- Adres, postcode, plaats, telefoon, e-mail
- Deze worden gebruikt in footer + contactpagina

### EBOH — Social media
- Facebook, Instagram, YouTube, X (Twitter) URLs

Klik **Publiceren** zodra alles staat.

---

## 7. Afbeeldingen plaatsen

Het thema gebruikt fallback-afbeeldingen uit `/wp-content/themes/eboh-theme/assets/images/`. Als je deze wilt overschrijven, vervang dan de bestaande bestanden:

- `logo-eboh.png` — clublogo
- `hero-1.jpg`, `hero-2.jpg`, `hero-3.jpg` — hero achtergronden
- `about-eboh.jpg` — over-de-club sectie
- `action-photo.jpg` — CTA banner
- `team-placeholder.jpg` — fallback teamfoto
- `sponsor-placeholder.png` — fallback sponsor logo

> **Tip:** Liever via Customizer afbeeldingen zetten dan bestanden in de theme-folder vervangen. Dat blijft behouden bij theme-updates.

---

## 8. Teams toevoegen

1. Ga naar **Teams → Nieuw team**.
2. Vul titel in (bv. `Heren 1`).
3. Voeg uitgelichte afbeelding toe.
4. Vul **excerpt** in als korte positiebeschrijving (bv. `Zaterdag 1e klasse`).
5. Wijs een **Team categorie** toe (bv. Senioren, Jeugd, Dames). Categorieën beheer je via **Teams → Categorieën**.
6. Publiceer.

Teams verschijnen automatisch op de Teams-pagina en kunnen ook met `[eboh_team_grid category="senioren"]` op andere pagina's worden geplaatst.

---

## 9. Sponsors toevoegen

1. Ga naar **Sponsors → Nieuwe sponsor**.
2. Titel = bedrijfsnaam.
3. Upload sponsor logo als uitgelichte afbeelding (transparante PNG werkt het beste).
4. In het custom field `_sponsor_url` zet je de website van de sponsor.
5. Publiceer.

Sponsors verschijnen via `[eboh_sponsor_grid]` of automatisch op de Sponsoring-pagina.

---

## 10. Nieuwsberichten

Gebruik de standaard **Berichten** sectie. Maak categorieën aan zoals:
- `wedstrijd`
- `jeugd`
- `club-nieuws`

Deze slugs gebruik je in de filter-tabs op de Nieuws-pagina.

---

## 11. Verificatie checklist

Na installatie controleren:

- [ ] Homepage laadt zonder fouten
- [ ] Logo zichtbaar in header en footer
- [ ] Menu zichtbaar (desktop én mobile)
- [ ] Mobile menu opent en sluit
- [ ] Sticky header werkt bij scroll
- [ ] Hero CTA-knop linkt correct
- [ ] Nieuws filter (AJAX) werkt zonder page reload
- [ ] Teams pagina toont team-cards met juiste categorieën
- [ ] Programma sjabloon toont
- [ ] Sponsoring pagina toont sponsor logos
- [ ] Contact pagina toont adres + kaart
- [ ] Footer toont contactgegevens en social icons
- [ ] Geen 404's in browser console (controleer Network tab)
- [ ] Geen PHP notices/warnings (zet `WP_DEBUG` tijdelijk aan)

---

## 12. Troubleshooting

### Custom Post Types geven 404
→ Ga naar **Instellingen → Permalinks** en klik gewoon op **Opslaan**. Dit ververst de rewrite rules.

### AJAX nieuws filter werkt niet
→ Open browser console. Check of `ebohData.nonce` en `ebohData.ajaxUrl` aanwezig zijn. Zo niet: clear cache (browser + eventueel WP Rocket / W3TC).

### Mobile menu opent niet
→ Controleer of `eboh-scripts.js` geladen wordt (Network tab). Cache plugin? Purge.

### Afbeeldingen worden niet getoond
→ Controleer dat `assets/images/` aanwezig is met de fallback bestanden. Anders Customizer afbeeldingen opnieuw uploaden.

### Sticky header overlapt content
→ Controleer dat `body` de class `eboh-site` krijgt (wordt automatisch toegevoegd via `body_class()` filter in `functions.php`).

### Theme update overschrijft aanpassingen
→ Maak een **child theme** voor maatwerk. Plaats overrides in het child theme, niet direct in `eboh-theme/`.

---

## 13. Aanbevolen plugins

| Plugin | Doel |
|---|---|
| **Wordfence** of **Solid Security** | Security |
| **WP Rocket** of **LiteSpeed Cache** | Performance |
| **Yoast SEO** of **Rank Math** | SEO |
| **UpdraftPlus** | Backups |
| **Contact Form 7** of **WPForms** | Formulieren (lid worden, contact) |
| **Smush** of **ShortPixel** | Image optimization |

---

## 14. Maintenance

- Houd WordPress core en plugins up-to-date.
- Maak wekelijks (of dagelijks) een backup.
- Test na elke WP-update of het thema nog correct rendert op een staging-omgeving.
- Monitor PHP error log via host.

---

## 15. Versie

**1.0.0** — productieklaar (2026-04-07)
Standalone WordPress theme. Geen Enfold dependency.
