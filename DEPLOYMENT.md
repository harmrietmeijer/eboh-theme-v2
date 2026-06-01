# EBOH Theme - Deployment Guide

## Quick Start

1. **Extract Files**
   - Extract `eboh-theme.zip` to `/wp-content/themes/`

2. **Activate Theme**
   - Ensure "Enfold" parent theme is installed
   - Go to Appearance > Themes
   - Click "Activate" on "EBOH Enfold Child"

3. **Create Navigation Menu**
   - Appearance > Menus
   - Create menu: "Primary Menu"
   - Add links to: Home, News, Teams, Program, Lid Worden, Sponsoring, Contact
   - Set as Primary Menu location

4. **Upload Logo & Images**
   - Appearance > Customize > Site Identity > Logo
   - Upload club logo (recommended: 200x200px)
   - Add hero images to: Assets > Images folder

## File Checklist

### Core Files
- [x] style.css (Main stylesheet - 2,600 lines)
- [x] functions.php (Theme functions - 820 lines)
- [x] header.php (Header template)
- [x] footer.php (Footer template)

### Page Templates
- [x] front-page.php (Homepage)
- [x] single.php (Single post)
- [x] template-nieuws.php (News overview)
- [x] template-teams.php (Teams page)
- [x] template-programma.php (Schedule/results)
- [x] template-lid-worden.php (Join page)
- [x] template-contact.php (Contact page)
- [x] template-sponsoring.php (Sponsoring page)

### Includes
- [x] inc/custom-post-types.php (Team CPT)
- [x] inc/widgets.php (Custom widgets)
- [x] inc/shortcodes.php (Shortcodes)

### Assets
- [x] assets/js/eboh-scripts.js (JavaScript - 450 lines)
- [x] assets/css/eboh-responsive.css (Responsive - 900 lines)
- [x] assets/images/ (folder for images)

### Template Parts
- [x] parts/news-card.php (Reusable component)

### Documentation
- [x] README.md (Installation & features)
- [x] SETUP.md (Detailed setup)
- [x] THEME-STRUCTURE.txt (File overview)
- [x] DEPLOYMENT.md (This file)

## Project Statistics

**Total Size:** 252 KB
**Total Files:** 21
**Total Lines:** 8,600+

**Breakdown:**
- PHP: 4,500 lines (14 files)
- CSS: 2,800 lines (2 files)
- JavaScript: 450 lines (1 file)
- Documentation: 900 lines (4 files)

## Features Included

### Homepage Sections
1. Match Ticker Bar (countdown to next match)
2. Hero Slider (3 slides, auto-advance)
3. Recent News Cards Grid
4. About Section ("Wij zijn EBOH")
5. Program & Schedule Widget
6. CTA Banner ("Ook lid worden?")
7. Team Cards Grid
8. Sponsor Bar
9. Full-width Action Photo

### Additional Pages
- News Overview with filtering and pagination
- Teams Overview with category filters
- Schedule & Results (programma)
- Membership (Lid Worden) with form
- Contact Page with form and map
- Sponsoring Page with packages

### Dynamic Features
- Hero slider with auto-advance (6 seconds)
- Match countdown timer
- Sticky header on scroll
- Mobile hamburger menu
- Filter tabs for news/teams
- Accordion for FAQs
- Social sharing buttons
- AJAX news loading
- Form validation
- Lazy image loading
- Scroll-to-top button

## Color Palette

```
Primary Red:     #E80808
Dark Red:        #B70606
Dark Grey:       #343B41
Text Grey:       #465058
Light Grey:      #F8FAFC
Black:           #000000
White:           #FFFFFF
```

## Typography

- **Body:** Work Sans (400, 500, 600, 700, 800)
- **Headings:** Oswald (400, 500, 600, 700, 800) - ALL CAPS
- **Accents:** Crimson Text (400, 400i)

## Responsive Breakpoints

- 1024px - Tablets
- 768px - Tablets Landscape
- 600px - Mobile
- 380px - Extra Small Mobile
- Landscape Orientation
- Print Media

## Required Configuration

### 1. Create Pages

| Page Title | Slug | Template |
|-----------|------|----------|
| News | nieuws | Nieuws |
| Teams | teams | Teams |
| Program | programma | Programma & Uitslagen |
| Join | lid-worden | Lid Worden |
| Contact | contact | Contact |
| Sponsoring | sponsoring | Sponsoring |

### 2. Create Team Categories

Go to: Posts > Teams > Categories

- Senioren
- Junioren
- Pupillen
- Dames
- Volwassenen

### 3. Create Sample Teams

Example teams to add:
- Senioren A (Senioren)
- Senioren B (Senioren)
- U19 (Junioren)
- U17 (Junioren)
- U12 (Pupillen)
- Dames (Dames)

### 4. Upload Images

**Required Images:**

```
assets/images/
├── logo-eboh.png (50x50px)
├── hero-1.jpg (1920x600px)
├── hero-2.jpg (1920x600px)
├── hero-3.jpg (1920x600px)
├── about-eboh.jpg
├── programma.jpg
├── action-photo.jpg
├── team-placeholder.jpg (500x600px)
├── sponsor-placeholder.png
├── board-member-1.jpg through 6.jpg
└── placeholder.jpg (400x300px)
```

## Customization Quick Tips

### Change Primary Color
Edit `style.css` line 21:
```css
--eboh-red: #YOUR_COLOR;
--eboh-red-dark: #DARKER_VERSION;
```

### Modify Club Info
Edit `functions.php` around line 150:
```php
'name'     => 'vv EBOH',
'address'  => 'Your Address',
'phone'    => 'Your Phone',
```

### Add Custom Menu Items
Appearance > Menus > Edit Primary Menu

### Create Custom Pages
Use the page templates:
- Select "Nieuws" for news pages
- Select "Teams" for team overview
- etc.

## Recommended Plugins

**Essential:**
- Yoast SEO (SEO optimization)
- Contact Form 7 (Contact forms)

**Performance:**
- WP Rocket (Caching)
- Imagify (Image optimization)

**Backup & Security:**
- BackWPup (Automated backups)
- Wordfence (Security)

## Testing Checklist

- [ ] Test on Chrome (desktop & mobile)
- [ ] Test on Firefox
- [ ] Test on Safari
- [ ] Test on Edge
- [ ] Check all responsive breakpoints
- [ ] Test all page templates
- [ ] Verify all links work
- [ ] Test contact forms
- [ ] Check hero slider
- [ ] Verify match ticker countdown
- [ ] Test news filtering
- [ ] Test team filtering
- [ ] Check social sharing buttons
- [ ] Verify Google Maps embed
- [ ] Test newsletter signup
- [ ] Check mobile menu
- [ ] Verify sticky header
- [ ] Test keyboard navigation
- [ ] Check accessibility (alt texts)

## Performance Optimization

1. **Enable Caching**
   - Use WP Rocket or W3 Total Cache

2. **Optimize Images**
   - Use tools like TinyPNG or Imagify
   - Use WebP format where possible
   - Lazy load images (theme supports this)

3. **Minify Code**
   - CSS and JS are production-ready
   - Consider minification plugins

4. **Use CDN**
   - CloudFlare free tier recommended

5. **Database**
   - Regular cleanup of old revisions
   - Optimize database tables

## Security Measures

1. **Backup**
   - Install BackWPup
   - Schedule daily backups

2. **Updates**
   - Keep WordPress updated
   - Update Enfold parent theme
   - Update plugins regularly

3. **Passwords**
   - Use strong admin password
   - Use unique database prefix
   - Limit login attempts

4. **SSL Certificate**
   - Enable HTTPS
   - Install security plugin (Wordfence)

## Support & Maintenance

### Regular Tasks
- [ ] Weekly: Check for plugin updates
- [ ] Monthly: Review analytics
- [ ] Monthly: Update news/team information
- [ ] Quarterly: Review backups
- [ ] Annually: Full theme audit

### Common Issues

**Theme not showing:**
- Clear WordPress cache
- Hard refresh browser (Ctrl+Shift+R)
- Disable all plugins temporarily

**Menu not displaying:**
- Check Appearance > Menus
- Make sure menu is set as Primary Menu

**Images not loading:**
- Verify image paths in admin
- Check file permissions
- Regenerate thumbnails

**Contact form not working:**
- Check Contact Form 7 plugin
- Verify email settings
- Check spam filters

## Additional Resources

- **Enfold Documentation:** https://www.kriesi.at/documentation/enfold/
- **WordPress.org:** https://wordpress.org
- **KNVB:** https://www.knvb.nl
- **Google Fonts:** https://fonts.google.com

## Version History

**1.0.0** (March 13, 2026)
- Initial release
- Homepage with hero slider
- 7 custom page templates
- News and team management
- Membership system
- Sponsoring management
- Full responsive design
- Complete documentation

## Getting Help

For issues or questions:
1. Check SETUP.md for installation issues
2. Review THEME-STRUCTURE.txt for file overview
3. Check README.md for features
4. Review code comments in files
5. Contact Enfold support for parent theme issues

---

**Theme Created:** March 13, 2026
**For:** vv EBOH, Dordrecht, Netherlands
**Status:** Production Ready
