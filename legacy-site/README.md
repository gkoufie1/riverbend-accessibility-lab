# Riverbend Legacy Site (Demo)

A deliberately flawed WordPress site for a **fictional** regional agency, used in the [audit lab](../audit-lab/README.md). It copies a site inherited from several vendors: Astra theme, Elementor and Beaver Builder pages, Contact Form 7, and 13 planted accessibility barriers (B01–B13).

**Runs locally only**, at http://localhost:8080. Nothing is exposed to the internet.

| | |
|---|---|
| Site | http://localhost:8080 |
| Admin | http://localhost:8080/wp-admin |
| Login | `admin` / `riverbend-demo` |

## Everyday use

Run these from this folder (`riverbend/legacy-site`) with Docker Desktop running:

```sh
docker compose up -d      # start (your changes are kept)
docker compose stop       # stop (your changes are kept)
```

## Reset to the original broken site

This **erases every change you've made**, including your fixes. Take screenshots and notes first.

```sh
docker compose down -v
docker compose up -d
docker compose run --rm wpcli sh /scripts/setup.sh
```

In Git Bash, put `MSYS_NO_PATHCONV=1 ` in front of the last command so the `/scripts` path isn't rewritten.

The last command takes a few minutes. It reinstalls WordPress, the theme and plugins, re-imports the images and rebuilds every page with the barriers.

## What's in here

| Path | What it does |
|---|---|
| `docker-compose.yml` | WordPress (PHP 8.3 + Apache), MariaDB, and a one-shot WP-CLI container |
| `scripts/setup.sh` | Installs WordPress, Astra, Elementor, Beaver Builder Lite and Contact Form 7, imports images, runs the build |
| `scripts/build-site.php` | Creates the pages, posts, menu, form and styles, with each barrier marked `B01`–`B13` in comments |
| `scripts/make-images.py` | Makes the oversized "camera export" photos and the hero banner with text baked in (already run; outputs are in `assets/`) |
| `mu-plugins/legacy-vendor-tweaks.php` | The "previous vendor's" plugin that turns off WordPress image downscaling |
| `mu-plugins/riverbend-design.php` + `riverbend-design/` | Design layer in a government-portal style: fictional seal header with board officers, utility links and search, navy menu bar, breadcrumbs, content card, and a Quick Links / Upcoming Events sidebar. The footer uses the Atlanta night photo. It doesn't touch the planted barriers, and it adds no new axe findings. |

**Tip:** don't read `build-site.php` before you've done the audit (Modules 2–5). Finding the problems yourself is the point.

## Notes

- **Photo credit (footer):** Atlanta night skyline, "Jackson Street Bridge, Atlanta, United States" by Joey Kyber (Unsplash), via Wikimedia Commons, **CC0** public domain. No attribution is required, but it's credited here. Source: `assets/atlanta-night-jackson-st.jpg`.

- The Public Comment form runs in Contact Form 7 **demo mode**. Submissions show a success message but no email is sent.
- The map on Home loads from OpenStreetMap, so it needs an internet connection.
