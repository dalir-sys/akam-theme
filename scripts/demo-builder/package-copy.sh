#!/usr/bin/env bash
# package-copy.sh — کپی تمیز یک دمو برای ساخت بسته Duplicator (قالب اینکدشده، بدون تنظیمات توسعه)
# Makes a clean copy of a local demo for packaging with Duplicator Pro: encoded theme from dist/,
# the original wp-config from the base Duplicator archive, no local admin user, production URL.
#
# Usage: ./package-copy.sh DEMO_NUMBER BASE_ARCHIVE LIVE_URL [version]
#   ./package-copy.sh 4 ../../../akam-demo-2/pkg/archive.zip https://tadris.webmz.ir/demo-04
set -euo pipefail
N="$1"; BASE_ARCHIVE="$(realpath "$2")"; LIVE="${3%/}"; VERSION="${4:-1.5.0}"
REPO="$(cd "$(dirname "$0")/../.." && pwd)"
SRC="$(realpath "$REPO/../akam-demo-$N")"; DST="$(realpath -m "$REPO/../akam-pkg-$N")"
SRCDB="akam_demo$N"; DB="akam_pkg$N"
DIST="$REPO/dist/$VERSION"
[[ -d "$DIST/akam" && -d "$DIST/akam-child" ]] || { echo "Run scripts/build-release.sh $VERSION first" >&2; exit 1; }

rm -rf "${DST:?}" && mkdir -p "$DST"
cp -a "$SRC/site" "$DST/site"; cp -a "$SRC/bin" "$DST/bin"
rm -f "$DST/site/wp-content/debug.log"
rm -rf "$DST/site/wp-content/uploads/akam-demo-files" "$DST/site/wp-content/uploads/wc-logs/"*.log

# Encoded theme only.
rm -rf "$DST/site/wp-content/themes/akam" "$DST/site/wp-content/themes/akam-child"
cp -a "$DIST/akam" "$DIST/akam-child" "$DST/site/wp-content/themes/"
grep -q "rtlLicenseFileHash" "$DST/site/wp-content/themes/akam/functions.php" && { echo "functions.php is not encoded" >&2; exit 1; }

mysql -e "DROP DATABASE IF EXISTS $DB; CREATE DATABASE $DB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;
GRANT ALL ON $DB.* TO 'akam'@'localhost'; GRANT ALL ON $DB.* TO 'akam'@'127.0.0.1'; FLUSH PRIVILEGES;"
mysqldump --default-character-set=utf8mb4 "$SRCDB" | mysql --default-character-set=utf8mb4 "$DB"

# Original wp-config of the base demo, with this database.
unzip -p "$BASE_ARCHIVE" 'dup-installer/dup_descriptors_*/orig_files/source_site_wpconfig' > "$DST/site/wp-config.php"
sed -i -e "s/define( 'DB_NAME', '' );/define( 'DB_NAME', '$DB' );/" -e "s/define( 'DB_USER', '' );/define( 'DB_USER', 'akam' );/" \
	-e "s/define( 'DB_PASSWORD', '' );/define( 'DB_PASSWORD', 'akam' );/" -e "s/define( 'DB_HOST', '' );/define( 'DB_HOST', '127.0.0.1' );/" "$DST/site/wp-config.php"
grep -q "'DB_NAME', '$DB'" "$DST/site/wp-config.php" || { echo "Could not set DB_NAME in wp-config" >&2; exit 1; }

WP="php -d memory_limit=1024M $DST/bin/wp --allow-root --path=$DST/site"
OLD="$($WP --skip-plugins --skip-themes option get siteurl)"
$WP --skip-plugins --skip-themes search-replace "$OLD" "$LIVE" --all-tables --precise --quiet
$WP --skip-plugins --skip-themes search-replace "${OLD//\//\\/}" "${LIVE//\//\\/}" --all-tables --precise --quiet
$WP --skip-plugins --skip-themes search-replace "$SRC/site" "$DST/site" --all-tables --precise --quiet

# No development account, sessions or caches in the package.
if $WP --skip-plugins --skip-themes user get localadmin >/dev/null 2>&1; then
	$WP --skip-plugins --skip-themes user delete localadmin --reassign=1 --yes
fi
$WP --skip-plugins --skip-themes transient delete --all >/dev/null
mysql "$DB" -e "TRUNCATE wp_woocommerce_sessions; DELETE FROM wp_usermeta WHERE meta_key IN ('session_tokens','_woocommerce_persistent_cart_1');" 2>/dev/null || true
$WP plugin activate duplicator-pro
echo "Packaging copy ready: $DST ($DB, $LIVE)"
