# Launch Sports Management

WordPress build for Launch Sports Management, a women's basketball athlete
representation agency. Built by Studio Ubique.

The site is a custom theme (`launch-sports`) built from an approved static HTML
design. Every page is editable through ACF Pro; nothing that appears on screen
is hardcoded.

## What is in this repository

This is the full WordPress installation, minus the things that must not be in
source control (see `.gitignore`):

| Excluded | Why |
| --- | --- |
| `wp-config.php` | Database credentials and authentication salts |
| `wp-content/uploads/` | Media is content, restored from a backup or migration |
| `wp-content/plugins/advanced-custom-fields-pro/` | Commercial plugin, per-site licence |

Because of those exclusions a fresh clone is **not** a working site on its own.
See Setup below.

## The theme

`wp-content/themes/launch-sports/`

| Path | Contents |
| --- | --- |
| `inc/` | Setup, asset loading, template helpers, navigation, post types, ACF field groups, CF7 integration, legal-page helpers |
| `templates/` | Page templates: About, What We Do, Let's Talk, Legal |
| `template-parts/sections/` | One file per section of the design |
| `assets/css/` | Exactly two stylesheets: `desktop.css` then `responsive.css`, in that order |
| `assets/js/` | Motion layer (GSAP + Lenis), navigation, fallbacks, legal-page contents |

Two rules the build depends on:

- **`desktop.css` must load before `responsive.css`.** The responsive sheet
  overrides the desktop one by coming later, and in several places relies on
  source order rather than specificity.
- **Sections are direct siblings of the header.** Two mobile rules target
  `header ~ section`, so there is no `<main>` wrapper anywhere.

## Pages and how they are edited

| Page | Template | Edited in |
| --- | --- | --- |
| Home | `front-page.php` | Pages → Home |
| What We Do | `templates/page-what-we-do.php` | Pages → What We Do |
| About Launch | `templates/page-about.php` | Pages → About Launch |
| Let's Talk | `templates/page-lets-talk.php` | Pages → Let's Talk, plus Contact → Let's Talk enquiry |
| Privacy Policy | `templates/page-legal.php` | Pages → Privacy Policy (body in the Classic Editor) |
| 404 | `404.php` | Site settings → Page not found |

Header, footer, logos, menus and the 404 wording live under **Site settings**.

Two collections are post types because they are reordered often: **Players**
(`lsm_player`) and **Team** (`lsm_member`). The What We Do process steps and
register stages are ACF repeaters on that page instead — they are short,
text-only and belong to one page.

## Setup

1. Clone into a web root and create a database.
2. Copy `wp-config-sample.php` to `wp-config.php` and fill in the database
   credentials and fresh salts.
3. Install **Advanced Custom Fields Pro** from the vendor zip and activate it,
   then activate Classic Editor and Contact Form 7.
4. Activate the **Launch Sports Management** theme.
5. Import the database and the `uploads/` directory from a backup — the pages,
   ACF content, menus and the Contact Form 7 form all live there.

## Contact form

The Let's Talk form is a Contact Form 7 form. CF7 generates its own markup, so
`inc/contact.php` normalises that output back to the approved design's markup —
unwrapping CF7's grouping spans, restoring the design's classes on the radio and
consent labels, and converting the submit input into a `<button>`, which the
motion layer looks for.

Enquiries are emailed; nothing is stored in WordPress. **The live host needs SMTP
configured** or mail will fail silently.

## Known items before launch

- The privacy policy is a working draft with bracketed placeholders and needs
  legal review, particularly the section on under-18s.
- Contact Form 7 makes radio groups mandatory, so "I Am A" is required. The
  static design left it optional.
- Blog templates (`home.php`, `single.php`, `archive.php`, search) are not built.
