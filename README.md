# EBOH WordPress Theme

Standalone WordPress theme voor vv EBOH (Dordrecht, opgericht 1926).
Geen parent theme of page builder vereist — alles draait native op WordPress core.

---

## Snel installeren

1. Zip de map `eboh-theme/` (de map zelf, niet de inhoud).
2. WordPress Admin → **Weergave → Thema's → Nieuw thema toevoegen → Thema uploaden**.
3. Upload de zip en klik op **Activeren**.
4. Ga naar **Weergave → Aanpassen** om logo, kleuren, hero-tekst, contactgegevens en social links in te stellen.

> Minimaal vereist: WordPress 5.8+, PHP 7.4+ (PHP 8.1+ aanbevolen).

---

## Wat dit thema doet

- Volledig standalone — geen Enfold, geen Elementor, geen Divi.
- Custom Post Type: **Teams** (met taxonomy `team_category`). Er is geen sponsor-CPT; `[eboh_sponsor_grid]` toont nu vaste placeholders.
- Aangepaste page templates voor Nieuws, Teams, Programma, Lid Worden, Sponsoring en Contact.
- Mobile-first responsive met sticky header en mobile menu.
- AJAX nieuws filter (geen page reload).
- WordPress Customizer integratie voor alle dynamische content.
- Shortcodes voor flexibele content op pagina's.
- Brand-pure: kleuren, typografie en watermerken volgens EBOH huisstijl.

---

## Brand

| Element | Waarde |
|---|---|
| Primair rood | `#E80808` |
| Hover rood | `#B70606` |
| Donkergrijs | `#343B41` |
| Tekst grijs | `#465058` |
| Lichte achtergrond | `#F8FAFC` |
| Body font | Work Sans |
| Headings | Oswald (uppercase) |
| Accent | Crimson Text (serif italic) |

CSS variabelen staan in `style.css` onder `:root` — pas daar aan om kleuren globaal te wijzigen.

---

## Bestandsstructuur

```
eboh-theme/
├── style.css                    # Hoofdstylesheet + theme header
├── functions.php                # Setup, enqueues, customizer, AJAX
├── header.php                   # Site header met sticky nav
├── footer.php                   # Site footer
├── front-page.php               # Homepage
├── page.php / single.php        # Standaard pagina + post
├── archive.php / search.php     # Overzichten
├── 404.php                      # Foutpagina
├── template-nieuws.php          # Nieuws overzicht
├── template-teams.php           # Teams overzicht
├── template-programma.php       # Programma & uitslagen
├── template-lid-worden.php      # Inschrijfformulier
├── template-sponsoring.php      # Sponsorpakketten
├── template-contact.php         # Contact + info
├── inc/
│   ├── custom-post-types.php    # Team + Sponsor CPT registratie
│   ├── sportlink-api.php        # Sportlink-koppeling + shortcodes
│   ├── shortcodes.php           # Custom shortcodes
│   └── widgets.php              # Footer/sidebar widgets
├── parts/
│   └── news-card.php            # Herbruikbare nieuwskaart
├── assets/
│   ├── css/eboh-responsive.css  # Responsive overrides
│   ├── js/eboh-scripts.js       # Mobile menu, AJAX, sticky header
│   └── images/                  # Logo, hero, action photos
└── screenshot.png               # Theme thumbnail (1200x900)
```

---

## Shortcodes

| Shortcode | Doel |
|---|---|
| `[eboh_match_ticker]` | Sticky bar met eerstvolgende wedstrijd |
| `[eboh_news_grid category="club-nieuws" limit="3"]` | Grid van nieuwsposts |
| `[eboh_team_grid category="senioren" limit="6"]` | Grid van teamkaarten |
| `[eboh_sponsor_grid]` | Logo grid van sponsors |

---

## Customizer

Panelen en secties staan in `functions.php` (`eboh_customize_register`): Organisatie, Competitie en Homepage (Hero, Next Match, About, CTA, Parallax, Club Information). Niet alle velden die de templates lezen hebben een Customizer-control; zie CLAUDE.md, "Bekende afwijkingen" #7.

---

## Versie

Het versienummer staat in de header van `style.css` en wordt als cache-buster voor CSS/JS gebruikt.

## Licentie

GPL v2 of later.

## Club info

**vv EBOH**
Sportcomplex Schenkeldijk 6
3328 LE Dordrecht
+31 (0)78 - 613 2834 — info@eboh.nl

---

## Ontwikkelen

Zie `CLAUDE.md`. Controleren: `scripts/verify.sh`. Uploadpakket maken: `scripts/package.sh`.
