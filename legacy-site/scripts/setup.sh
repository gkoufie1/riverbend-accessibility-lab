#!/bin/sh
# Builds the legacy Riverbend site from scratch. Run from legacy-site/:
#   docker compose up -d
#   docker compose run --rm wpcli sh /scripts/setup.sh
set -e

until [ -f /var/www/html/wp-config.php ]; do sleep 2; done
until php -r 'new mysqli("db", "wordpress", "wordpress", "wordpress");' 2>/dev/null; do
  echo "Waiting for the database..."; sleep 3
done

if ! wp core is-installed 2>/dev/null; then
  wp core install --url=http://localhost:8080 \
    --title="Riverbend Regional Commission (Demo)" \
    --admin_user=admin --admin_password=riverbend-demo \
    --admin_email=admin@riverbend.test --skip-email
fi

wp option update blogdescription "Demo site - not a real agency"
wp theme install astra --activate
wp plugin install elementor beaver-builder-lite-version contact-form-7 --activate
wp plugin delete akismet hello 2>/dev/null || true

echo "Importing full-size images (no alt text)..."
HERO=$(wp media import /assets/hero-public-hearing.jpg --porcelain)
C1=$(wp media import /assets/carousel-2.jpg --porcelain)
C2=$(wp media import /assets/carousel-3.jpg --porcelain)
C3=$(wp media import /assets/carousel-4.jpg --porcelain)
C4=$(wp media import /assets/carousel-5.jpg --porcelain)

wp eval-file /scripts/build-site.php "$HERO" "$C1" "$C2" "$C3" "$C4"
wp rewrite flush
echo "Done: http://localhost:8080  (admin / riverbend-demo)"
