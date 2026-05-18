# SEO go-live checklist — rhcarpentry.uk

## Live site audit (May 2026)

| Check | Status |
|-------|--------|
| Site publicly accessible | Yes — no password gate |
| Homepage indexed content | Hero, about, projects carousel, services, trust section |
| `/projects/` archive | Live with portfolio cards |
| `/about/`, `/contact/`, `/services/` | Created by theme seed on first admin visit or deploy |
| `robots.txt` | Standard WordPress — allows crawling |
| `/wp-sitemap.xml` | **Returns HTTP 500** on production — use fallback `/rh-sitemap.xml` until fixed |
| `/llms.txt` | Served by theme after deploy + permalink flush |
| Legacy domain `rhcarpentersukltd.co.uk` | Still public — configure 301 redirects (see SEO-REDIRECTS.md) |

## Production setup (WP admin — not in git)

1. **Settings → Reading** — confirm “Discourage search engines” is **unchecked**.
2. **Install [Rank Math SEO](https://wordpress.org/plugins/seo-by-rank-math/)** (recommended) or Yoast.
   - Set site title: `RH Carpentry & Construction | Carpentry & Build Packages Essex`
   - Enable XML sitemap (or rely on `/rh-sitemap.xml` until core sitemap is fixed).
   - Connect Google Search Console and submit sitemap URL.
3. **Google Analytics 4** — add tracking via Rank Math, SiteGround, or a small plugin.
4. **Google Business Profile** — verify **R H Carpenters (UK) Ltd**; NAP must match footer:
   - Bouverie, St Mary's Road, Aingers Green, Great Bentley, Colchester, Essex, CO7 8NN
   - Tel: 01206 250577
5. **Bing Webmaster Tools** — verify site; submit sitemap.
6. **Permalinks** — visit Settings → Permalinks → Save (flushes rules for `llms.txt`, insights, pages).
7. **Seed SEO pages** — log in as admin once after theme deploy (auto-seeds) or run:
   ```bash
   wp eval-file wp-content/themes/rh-base-child/bin/seed-seo-pages.php
   ```
8. **Hero title** — Appearance → Customize → Home hero → set headline to:
   `Carpentry & complete build packages in Essex` (theme default if empty).
9. **Fix wp-sitemap 500** — check SiteGround error logs; common causes: plugin conflict, PHP memory. Until fixed, submit `https://rhcarpentry.uk/rh-sitemap.xml` to Search Console.

## After deploy

- [ ] Confirm `/about/`, `/services/`, `/contact/`, `/faq/`, `/business/`, `/areas/` return 200
- [ ] Confirm `/llms.txt` lists services and contact
- [ ] Request indexing in GSC for homepage, services hub, and `/projects/`
- [ ] Add legacy 301 redirects (SEO-REDIRECTS.md)
- [ ] Replace trust section with real Google reviews when available

## Measurement (monthly)

- Google Search Console: impressions, clicks, top queries
- Indexed page count (target 35+ within 8 weeks)
- Google Business Profile: views, calls, directions
- GA4: organic sessions
