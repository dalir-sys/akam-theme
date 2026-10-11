#!/usr/bin/env bash
# راه‌اندازی دموهای محلی آکام (دمو ۱، ۲ و ۳) برای توسعه
# Local dev environment: restores a Duplicator demo package from its GitHub release,
# imports it into MariaDB, and symlinks this repo's raw akam/ and akam-child/ into it.
#
# Usage: ./scripts/setup-demo.sh [demo-number] [target-dir]
#   demo-number  1, 2 or 3 (default 3)
#   target-dir   default: ../akam-demo-<n>
# Each demo gets its own database (akam_demo<n>) and port (8081, 8082, 8080).
# Then:  php -S 127.0.0.1:<port> -t <target-dir>/site <target-dir>/router.php
set -euo pipefail

REPO_DIR="$(cd "$(dirname "$0")/.." && pwd)"
DEMO="${1:-3}"

case "$DEMO" in
	1)
		ASSET_URL="https://github.com/dalir-sys/akam-theme/releases/download/akam-installer/demo_1_9686c7ded46b2b036706_20260805083539_archive.zip"
		ASSET_SHA256="9828cba4c3ebcd8f5c14ab80efbda58d53707fcfd3bb98bb43e148b9b89a2af1"
		PORT=8081
		;;
	2)
		ASSET_URL="https://github.com/dalir-sys/akam-theme/releases/download/akam-installer/demo_2_4765e2a7d54c430b7385_20260805083541_archive.zip"
		ASSET_SHA256="dffbf940fd097b9e037c373bbcad174fdc5edf4c46d62c8966a4de80b8204f71"
		PORT=8082
		;;
	3)
		ASSET_URL="https://github.com/dalir-sys/akam-theme/releases/download/akam-installer-demo3/demo_3_aeaa929e0c52f0a71435_20260805083542_archive.zip"
		ASSET_SHA256="161bf043dcb0e3db5478aba063ae2916455e854a6e7c32df4a8251aa955538bb"
		PORT=8080
		;;
	*)
		echo "Usage: $0 [1|2|3] [target-dir]" >&2
		exit 1
		;;
esac

DEMO_DIR="$(realpath -m "${2:-$REPO_DIR/../akam-demo-$DEMO}")"
SITE_DIR="$DEMO_DIR/site"
WP_CLI_URL="https://github.com/wp-cli/wp-cli/releases/download/v2.12.0/wp-cli-2.12.0.phar"
NEW_URL="${AKAM_DEMO_URL:-http://localhost:$PORT}"
DB_NAME="akam_demo$DEMO" DB_USER=akam DB_PASS=akam

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
	[ -f pkg/outer.zip ] || curl -fSL --retry 4 -o pkg/outer.zip "$ASSET_URL"
	echo "$ASSET_SHA256  pkg/outer.zip" | sha256sum -c -
	unzip -q -o pkg/outer.zip -d pkg && rm pkg/outer.zip
	mv pkg/demo_"$DEMO"_*_archive.zip pkg/archive.zip
fi

# 3) Files
rm -rf "$SITE_DIR" && mkdir -p "$SITE_DIR"
unzip -q pkg/archive.zip -d "$SITE_DIR"
SQL_FILE="$(ls "$SITE_DIR"/dup-installer/dup_descriptors_*/db_dumps/*.sql)"
ORIG_CONFIG="$(ls "$SITE_DIR"/dup-installer/dup_descriptors_*/orig_files/source_site_wpconfig)"
# Original site path, read from the Duplicator package descriptor.
OLD_PATH="$(php -r '$d = json_decode(file_get_contents($argv[1]), true); echo rtrim($d["wpInfo"]["targetRoot"], "/");' "$(ls "$SITE_DIR"/dup-installer/dup_descriptors_*/archive.txt)")"

# 4) Database (utf8mb4 client charset is required, otherwise Persian text is double-encoded)
mysql --default-character-set=utf8mb4 "$DB_NAME" < "$SQL_FILE"
# Original site URL, as stored in the imported database.
OLD_URL="$(mysql -N --default-character-set=utf8mb4 "$DB_NAME" -e "SELECT option_value FROM wp_options WHERE option_name='siteurl'")"
OLD_URL="${OLD_URL%/}"
echo "Original site: $OLD_URL ($OLD_PATH)"

# 5) wp-config.php
php -r '
	[$_, $src, $dst, $url, $db] = $argv;
	$c = file_get_contents($src);
	$c = strtr($c, [
		"define( '\''DB_NAME'\'', '\'''\'' );"     => "define( '\''DB_NAME'\'', '\''$db'\'' );",
		"define( '\''DB_USER'\'', '\'''\'' );"     => "define( '\''DB_USER'\'', '\''akam'\'' );",
		"define( '\''DB_PASSWORD'\'', '\'''\'' );" => "define( '\''DB_PASSWORD'\'', '\''akam'\'' );",
		"define( '\''DB_HOST'\'', '\'''\'' );"     => "define( '\''DB_HOST'\'', '\''127.0.0.1'\'' );",
		"define( '\''WP_DEBUG'\'', false );"       => "define( '\''WP_DEBUG'\'', true );\ndefine( '\''WP_DEBUG_LOG'\'', true );\ndefine( '\''WP_DEBUG_DISPLAY'\'', false );\ndefine( '\''WP_ENVIRONMENT_TYPE'\'', '\''local'\'' );\ndefine( '\''WP_HOME'\'', '\''$url'\'' );\ndefine( '\''WP_SITEURL'\'', '\''$url'\'' );\ndefine( '\''DISALLOW_FILE_MODS'\'', true );\ndefine( '\''WP_HTTP_BLOCK_EXTERNAL'\'', true );",
	]);
	file_put_contents($dst, $c);
' "$ORIG_CONFIG" "$SITE_DIR/wp-config.php" "$NEW_URL" "$DB_NAME"
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
echo "  php -d memory_limit=512M -S 127.0.0.1:$PORT -t $SITE_DIR $DEMO_DIR/router.php"
echo "Site:  $NEW_URL   Admin: $NEW_URL/wp-admin  (localadmin / localadmin)"
