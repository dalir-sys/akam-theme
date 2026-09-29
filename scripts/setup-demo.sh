#!/usr/bin/env bash
# راه‌اندازی دموی محلی آکام (دمو ۳) برای توسعه
# Local dev environment: restores the Duplicator demo package from the GitHub release,
# imports it into MariaDB, and symlinks this repo's raw akam/ and akam-child/ into it.
#
# Usage: ./scripts/setup-demo.sh [target-dir]      (default: ../akam-demo)
# Then:  php -S 127.0.0.1:8080 -t <target-dir>/site <target-dir>/router.php
set -euo pipefail

REPO_DIR="$(cd "$(dirname "$0")/.." && pwd)"
DEMO_DIR="$(realpath -m "${1:-$REPO_DIR/../akam-demo}")"
SITE_DIR="$DEMO_DIR/site"
ASSET_URL="https://github.com/dalir-sys/akam-theme/releases/download/akam-installer-demo3/demo_3_aeaa929e0c52f0a71435_20260805083542_archive.zip"
ASSET_SHA256="161bf043dcb0e3db5478aba063ae2916455e854a6e7c32df4a8251aa955538bb"
WP_CLI_URL="https://github.com/wp-cli/wp-cli/releases/download/v2.12.0/wp-cli-2.12.0.phar"
OLD_URL="https://tadris.webmz.ir/demo-03"
OLD_PATH="/home/webmzir/tadris.webmz.ir/demo-03"
NEW_URL="${AKAM_DEMO_URL:-http://localhost:8080}"
DB_NAME=akam_demo DB_USER=akam DB_PASS=akam

mkdir -p "$DEMO_DIR/bin"
cd "$DEMO_DIR"

# 1) MariaDB
if ! command -v mysqld >/dev/null 2>&1; then
	sudo_cmd=""; [ "$(id -u)" -ne 0 ] && sudo_cmd="sudo"
	$sudo_cmd apt-get update -qq || true
	DEBIAN_FRONTEND=noninteractive $sudo_cmd apt-get install -y -qq mariadb-server
fi
mysqladmin ping >/dev/null 2>&1 || service mariadb start
mysql -e "DROP DATABASE IF EXISTS $DB_NAME; CREATE DATABASE $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;
CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';
CREATE USER IF NOT EXISTS '$DB_USER'@'127.0.0.1' IDENTIFIED BY '$DB_PASS';
GRANT ALL ON $DB_NAME.* TO '$DB_USER'@'localhost'; GRANT ALL ON $DB_NAME.* TO '$DB_USER'@'127.0.0.1'; FLUSH PRIVILEGES;"

# 2) Package (outer zip = installer.php + Duplicator archive)
if [ ! -f pkg/archive.zip ]; then
	mkdir -p pkg
	curl -fSL --retry 4 -o pkg/outer.zip "$ASSET_URL"
	echo "$ASSET_SHA256  pkg/outer.zip" | sha256sum -c -
	unzip -q -o pkg/outer.zip -d pkg && rm pkg/outer.zip
	mv pkg/demo_3_*_archive.zip pkg/archive.zip
fi

# 3) Files
rm -rf "$SITE_DIR" && mkdir -p "$SITE_DIR"
unzip -q pkg/archive.zip -d "$SITE_DIR"
SQL_FILE="$(ls "$SITE_DIR"/dup-installer/dup_descriptors_*/db_dumps/*.sql)"
ORIG_CONFIG="$(ls "$SITE_DIR"/dup-installer/dup_descriptors_*/orig_files/source_site_wpconfig)"

# 4) Database (utf8mb4 client charset is required, otherwise Persian text is double-encoded)
mysql --default-character-set=utf8mb4 "$DB_NAME" < "$SQL_FILE"

# 5) wp-config.php
php -r '
	[$_, $src, $dst, $url] = $argv;
	$c = file_get_contents($src);
	$c = strtr($c, [
		"define( '\''DB_NAME'\'', '\'''\'' );"     => "define( '\''DB_NAME'\'', '\''akam_demo'\'' );",
		"define( '\''DB_USER'\'', '\'''\'' );"     => "define( '\''DB_USER'\'', '\''akam'\'' );",
		"define( '\''DB_PASSWORD'\'', '\'''\'' );" => "define( '\''DB_PASSWORD'\'', '\''akam'\'' );",
		"define( '\''DB_HOST'\'', '\'''\'' );"     => "define( '\''DB_HOST'\'', '\''127.0.0.1'\'' );",
		"define( '\''WP_DEBUG'\'', false );"       => "define( '\''WP_DEBUG'\'', true );\ndefine( '\''WP_DEBUG_LOG'\'', true );\ndefine( '\''WP_DEBUG_DISPLAY'\'', false );\ndefine( '\''WP_ENVIRONMENT_TYPE'\'', '\''local'\'' );\ndefine( '\''WP_HOME'\'', '\''$url'\'' );\ndefine( '\''WP_SITEURL'\'', '\''$url'\'' );\ndefine( '\''DISALLOW_FILE_MODS'\'', true );\ndefine( '\''WP_HTTP_BLOCK_EXTERNAL'\'', true );",
	]);
	file_put_contents($dst, $c);
' "$ORIG_CONFIG" "$SITE_DIR/wp-config.php" "$NEW_URL"
rm -rf "$SITE_DIR"/dup-installer "$SITE_DIR"/*_installer-backup.php

# 6) Use this repo's raw themes instead of the encoded build shipped in the package
rm -rf "$SITE_DIR/wp-content/themes/akam" "$SITE_DIR/wp-content/themes/akam-child"
ln -s "$REPO_DIR/akam" "$SITE_DIR/wp-content/themes/akam"
ln -s "$REPO_DIR/akam-child" "$SITE_DIR/wp-content/themes/akam-child"

# 7) URL / path rewrite + local admin
[ -x bin/wp ] || { curl -fsSL -o bin/wp "$WP_CLI_URL" && chmod +x bin/wp; }
WP="php $DEMO_DIR/bin/wp --allow-root --path=$SITE_DIR"
$WP --skip-plugins --skip-themes search-replace "$OLD_URL" "$NEW_URL" --all-tables --precise
$WP --skip-plugins --skip-themes search-replace "${OLD_URL//\//\\/}" "${NEW_URL//\//\\/}" --all-tables --precise
$WP --skip-plugins --skip-themes search-replace "$OLD_PATH" "$SITE_DIR" --all-tables --precise
$WP plugin deactivate duplicator-pro || true
$WP user get localadmin >/dev/null 2>&1 || $WP user create localadmin localadmin@example.test --role=administrator --user_pass=localadmin
$WP elementor flush-css || true
$WP rewrite flush

# 8) Router for PHP's built-in server (pretty permalinks)
cat > "$DEMO_DIR/router.php" <<'PHP'
<?php
$root = __DIR__ . '/site';
$path = urldecode( parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) );
$file = $root . $path;
if ( $path !== '/' && is_file( $file ) && substr( $path, -4 ) !== '.php' ) {
	return false;
}
if ( is_dir( $file ) && is_file( rtrim( $file, '/' ) . '/index.php' ) ) {
	$path = rtrim( $path, '/' ) . '/index.php';
	$file = $root . $path;
}
if ( ! is_file( $file ) || substr( $path, -4 ) !== '.php' ) {
	$path = '/index.php';
	$file = $root . $path;
}
chdir( dirname( $file ) );
$_SERVER['SCRIPT_NAME']     = $path;
$_SERVER['SCRIPT_FILENAME'] = $file;
require $file;
PHP

echo
echo "Done. Start the server with:"
echo "  php -d memory_limit=512M -S 127.0.0.1:8080 -t $SITE_DIR $DEMO_DIR/router.php"
echo "Site:  $NEW_URL   Admin: $NEW_URL/wp-admin  (localadmin / localadmin)"
