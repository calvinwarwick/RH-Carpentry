# Legacy domain 301 redirects

Configure on **SiteGround** (Rank Math Redirections, SG Redirect, or `.htaccess`) when `rhcarpentry.uk` is the canonical site.

## Path redirects (rhcarpentersukltd.co.uk → rhcarpentry.uk)

| From | To |
|------|-----|
| `/work/{slug}/` | `https://rhcarpentry.uk/project/{slug}/` |
| `/projects/` | `https://rhcarpentry.uk/projects/` |
| `/about-us/` | `https://rhcarpentry.uk/about/` |
| `/contact/` | `https://rhcarpentry.uk/contact/` |
| `/` (and all other paths) | `https://rhcarpentry.uk/` (after path rules) |

## Rank Math example

1. SEO → Redirections → Add new
2. Source: `https://rhcarpentersukltd.co.uk/work/(.*)`
3. Destination: `https://rhcarpentry.uk/project/$1`
4. Type: 301

Repeat for `/about-us/`, `/contact/`, `/projects/`.

## Apache (.htaccess) on old domain (if parked)

```apache
RewriteEngine On
RewriteRule ^work/([^/]+)/?$ https://rhcarpentry.uk/project/$1/ [R=301,L]
RewriteRule ^projects/?$ https://rhcarpentry.uk/projects/ [R=301,L]
RewriteRule ^about-us/?$ https://rhcarpentry.uk/about/ [R=301,L]
RewriteRule ^contact/?$ https://rhcarpentry.uk/contact/ [R=301,L]
RewriteRule ^(.*)$ https://rhcarpentry.uk/ [R=301,L]
```

## Project slug mapping

Most legacy `/work/{slug}/` URLs match new `/project/{slug}/`. Verify any renamed projects in WordPress before enabling wildcards.
