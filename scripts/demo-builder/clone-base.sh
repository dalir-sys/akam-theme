#!/usr/bin/env bash
# clone-base.sh — یک دموی محلی را به‌عنوان پایه دموی جدید کپی می‌کند
# Copies a local demo (made with ../setup-demo.sh) into a new site with its own database and port.
#
# Usage: ./clone-base.sh SRC_DIR SRC_DB SRC_URL NEW_NUMBER NEW_PORT
#   demo 4 (file shop):   ./clone-base.sh ../../../akam-demo-2 akam_demo2 http://localhost:8082 4 8084
#   demo 5 (psychology):  ./clone-base.sh ../../../akam-demo-3 akam_demo3 http://localhost:8080 5 8085
set -euo pipefail
SRC="$(realpath "$1")"; SRCDB="$2"; SRCURL="$3"; N="$4"; PORT="$5"
REPO="$(cd "$(dirname "$0")/../.." && pwd)"
DST="$(realpath -m "$REPO/../akam-demo-$N")"; DB="akam_demo$N"; URL="http://localhost:$PORT"

rm -rf "${DST:?}" && mkdir -p "$DST/bin"
cp -a "$SRC/site" "$DST/site"
cp "$SRC/bin/wp" "$DST/bin/wp"
cp "$SRC/router.php" "$DST/router.php"
rm -f "$DST/site/wp-content/debug.log"

mysql -e "DROP DATABASE IF EXISTS $DB; CREATE DATABASE $DB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;
GRANT ALL ON $DB.* TO 'akam'@'localhost'; GRANT ALL ON $DB.* TO 'akam'@'127.0.0.1'; FLUSH PRIVILEGES;"
mysqldump --default-character-set=utf8mb4 "$SRCDB" | mysql --default-character-set=utf8mb4 "$DB"
sed -i -e "s#define( 'DB_NAME', '$SRCDB' );#define( 'DB_NAME', '$DB' );#" -e "s#$SRCURL#$URL#g" "$DST/site/wp-config.php"

# Work on this repo's raw themes.
rm -rf "$DST/site/wp-content/themes/akam" "$DST/site/wp-content/themes/akam-child"
ln -s "$REPO/akam" "$DST/site/wp-content/themes/akam"
ln -s "$REPO/akam-child" "$DST/site/wp-content/themes/akam-child"

WP="php $DST/bin/wp --allow-root --path=$DST/site --skip-plugins --skip-themes"
$WP search-replace "$SRCURL" "$URL" --all-tables --precise --quiet
$WP search-replace "${SRCURL//\//\\/}" "${URL//\//\\/}" --all-tables --precise --quiet
$WP search-replace "$SRC/site" "$DST/site" --all-tables --precise --quiet
echo "Cloned to $DST ($DB). Serve: php -S 127.0.0.1:$PORT -t $DST/site $DST/router.php"
