#!/usr/bin/env bash
# tadris-demos.sh — راه‌اندازی دموهای پوسته آکام روی یک VPS اوبونتو ۲۴.۰۴ (tadris.webmz.ir)
#
# Installs the five Akam demos at https://DOMAIN/demo-01 … /demo-05 on a server that already
# runs other production sites, without touching them:
#   - its own PHP-FPM master (systemd unit tadris-php-fpm) with the ionCube loader, started from
#     the system php-fpm binary with its own php.ini; the system PHP and its pools stay as they are
#   - its own nginx site file (only this server_name), checked with nginx -t before a reload
#   - its own databases and MySQL users (tadris_demoN), nothing else in MySQL is read or changed
#   - files under /var/www/tadris, /etc/tadris-demos and /opt/tadris-demos only
#   - apt is used only to add missing packages: it stops if a package would be upgraded, and
#     package scripts are not allowed to restart running services
#   - a Let's Encrypt certificate (webroot) once DOMAIN points at this server
#
# Usage (as root):
#   tadris-demos.sh [install]          install the demos that are not installed yet (idempotent)
#   tadris-demos.sh reinstall N        reinstall demo N from its package (drops its database)
#   tadris-demos.sh ssl                get the certificate and switch the demos to https
#   tadris-demos.sh update-theme PATH [--child]
#                                      replace wp-content/themes/akam of the WordPress site at PATH
#                                      with the encoded release (and akam-child with --child)
#   tadris-demos.sh status
#   tadris-demos.sh uninstall --yes    remove everything this script created
#
# Settings (environment):
#   DOMAIN=tadris.webmz.ir  VERSION=1.5.0  DEMOS="1 2 3 4 5"  PHP_VER=8.3
#   EMAIL=…            Let's Encrypt account e-mail (optional)
#   ADMIN_USER=tadris  ADMIN_PASS=…  ADMIN_EMAIL=…   admin account set on every demo
#   PKG_DIR=/root/akam  use akam-VERSION-demo-N.zip / akam-VERSION.zip from this folder instead of
#                       downloading them from the GitHub release (with SHA256SUMS*.txt if present)
#   IONCUBE_TGZ=…      local ioncube_loaders_lin_<arch>.tar.gz instead of downloading it
#   DB_CNF=/root/tadris-mysql.cnf   MySQL account of the demos (written on the first run, together
#                      with the SQL you run once as your MySQL admin to create it)
set -euo pipefail

DOMAIN="${DOMAIN:-tadris.webmz.ir}"
VERSION="${VERSION:-1.5.0}"
DEMOS="${DEMOS:-1 2 3 4 5}"
PHP_VER="${PHP_VER:-8.3}"
EMAIL="${EMAIL:-}"
ADMIN_USER="${ADMIN_USER:-tadris}"
ADMIN_EMAIL="${ADMIN_EMAIL:-admin@$DOMAIN}"
PKG_DIR="${PKG_DIR:-}"
IONCUBE_TGZ="${IONCUBE_TGZ:-}"
DB_CNF="${DB_CNF:-/root/tadris-mysql.cnf}"
DB_ACCOUNT=tadris_demos
SKIP_IONCUBE="${SKIP_IONCUBE:-0}"   # testing only: the theme license needs ionCube
SKIP_VERIFY="${SKIP_VERIFY:-0}"     # testing only: skip SHA-256 checks of the packages

RELEASE="https://github.com/dalir-sys/akam-theme/releases/download/v$VERSION"
WP_CLI_URL="https://github.com/wp-cli/wp-cli/releases/download/v2.12.0/wp-cli-2.12.0.phar"
APP=/var/www/tadris
PUB="$APP/public"
ETC=/etc/tadris-demos
OPT=/opt/tadris-demos
RUN=/run/tadris-php-fpm
SOCK="$RUN/php-fpm.sock"
UNIT=tadris-php-fpm
SYS_USER=tadris
CRED=/root/tadris-demos-credentials.txt
FPM_BIN="/usr/sbin/php-fpm$PHP_VER"
PHP_BIN="/usr/bin/php$PHP_VER"
SCAN_DIRS="$ETC/php/pre.d:/etc/php/$PHP_VER/fpm/conf.d:$ETC/php/post.d"
LOCK=/run/tadris-demos.lock

declare -A DEMO_TITLE=(
	[1]="آکادمی آموزش آنلاین"
	[2]="آموزش طراحی وب و برنامه‌نویسی"
	[3]="آموزشگاه زبان"
	[4]="آکام فایل — فروشگاه فایل"
	[5]="آکام ذهن — آکادمی روانشناسی"
)
declare -A DEMO_DESC=(
	[1]="دوره‌های ویدیویی، پادکست و ویدیوهای رایگان"
	[2]="فروش دوره و محصولات دیجیتال"
	[3]="استوری، پلی‌لیست ویدیویی و معرفی اساتید"
	[4]="قالب، افزونه، فونت، فایل گرافیکی و کتاب الکترونیک"
	[5]="دوره‌ها، روانشناسان و بسته‌های مشاوره"
)
FA_DIGITS=(۰ ۱ ۲ ۳ ۴ ۵ ۶ ۷ ۸ ۹)
NGINX_USER="$(awk '$1 == "user" {gsub(";", "", $2); print $2; exit}' /etc/nginx/nginx.conf 2>/dev/null || true)"
NGINX_USER="${NGINX_USER:-www-data}"
declare -A DEMO_COLOR=([1]="#6d28d9" [2]="#7c3aed" [3]="#e11d48" [4]="#3b5bfd" [5]="#0d9488")

log() { printf '\033[1;34m==>\033[0m %s\n' "$*"; }
warn() { printf '\033[1;33mWARNING:\033[0m %s\n' "$*" >&2; }
die() { printf '\033[1;31mERROR:\033[0m %s\n' "$*" >&2; exit 1; }
have_systemd() { [[ -d /run/systemd/system ]]; }
rand() { local r; r="$(head -c 1024 /dev/urandom | LC_ALL=C tr -dc 'A-Za-z0-9')"; printf '%s' "${r:0:${1:-24}}"; }
slug() { printf 'demo-%02d' "$1"; }

# Paths this script writes must stay inside its own folders.
guard_path() {
	local p; p="$(realpath -m "$1")"
	case "$p" in
		/var/www/hamishegi|/var/www/hamishegi/*) die "Refusing to touch $p" ;;
	esac
}

# All demo databases use one MySQL account that can only reach databases named tadris_*.
# The script never uses any other MySQL account: you create this one once as your MySQL admin.
mysql_db() { mysql --defaults-file="$DB_CNF" "$@"; }
db_cnf_get() { awk -F= -v k="$1" '$1 ~ "^ *"k" *$" {sub(/^[^=]*= */, ""); print; exit}' "$DB_CNF"; }

require_db_account() {
	local sqlfile=/root/tadris-mysql-setup.sql pass
	if [[ ! -f "$DB_CNF" ]]; then
		pass="$(rand 24)-Ta9_$(rand 4)"
		( umask 077; printf '[client]\nuser=%s\npassword=%s\nhost=127.0.0.1\n' "$DB_ACCOUNT" "$pass" > "$DB_CNF" )
	fi
	if ! mysql_db -N -e "CREATE DATABASE IF NOT EXISTS tadris_check; DROP DATABASE tadris_check;" >/dev/null 2>&1; then
		pass="$(db_cnf_get password)"
		( umask 077; cat > "$sqlfile" <<-SQL
			CREATE USER IF NOT EXISTS '$DB_ACCOUNT'@'localhost' IDENTIFIED BY '$pass';
			CREATE USER IF NOT EXISTS '$DB_ACCOUNT'@'127.0.0.1' IDENTIFIED BY '$pass';
			GRANT ALL PRIVILEGES ON \`tadris\\_%\`.* TO '$DB_ACCOUNT'@'localhost';
			GRANT ALL PRIVILEGES ON \`tadris\\_%\`.* TO '$DB_ACCOUNT'@'127.0.0.1';
		SQL
		)
		cat >&2 <<-MSG

			The demos need their own MySQL account ($DB_ACCOUNT), allowed to use only databases
			named tadris_*. Create it once with your MySQL admin login, then run this script again:

			  mysql --no-defaults -u root -p < $sqlfile

			(or run the four statements in $sqlfile from any MySQL admin client)
		MSG
		exit 2
	fi
	rm -f "$sqlfile"
	# wp-config.php uses the unix socket when MySQL has one, otherwise TCP.
	local sock; sock="$(mysql_db -N -e 'SELECT @@socket' 2>/dev/null || true)"
	if [[ -n "$sock" && -S "$sock" ]]; then DB_HOST=localhost; else DB_HOST=127.0.0.1; fi
}

wp_cli() {
	local path="$1"; shift
	"$PHP_BIN" -d memory_limit=1024M "$OPT/bin/wp" --allow-root --path="$path" "$@"
}

# ---------------------------------------------------------------------------------------------
# Checks

preflight() {
	[[ "$(id -u)" -eq 0 ]] || die "Run as root"
	local os; os="$(. /etc/os-release && echo "$ID $VERSION_ID")"
	[[ "$os" == "ubuntu 24.04" ]] || warn "Written for Ubuntu 24.04, this is $os"
	command -v nginx >/dev/null || die "nginx is not installed"
	command -v mysql >/dev/null || die "The mysql client is not installed"
	local free; free="$(df -Pk /var/www 2>/dev/null | awk 'NR==2 {print $4}')"
	[[ -z "$free" || "$free" -gt $((8 * 1024 * 1024)) ]] || die "Need at least 8 GB free on /var/www"
	exec 9>"$LOCK"; flock -n 9 || die "Another run is in progress"
}

# ---------------------------------------------------------------------------------------------
# Packages

apt_add_missing() {
	local missing=() p
	for p in "$@"; do
		dpkg-query -W -f='${Status}' "$p" 2>/dev/null | grep -q 'install ok installed' || missing+=("$p")
	done
	[[ ${#missing[@]} -eq 0 ]] && return 0
	log "Installing missing packages: ${missing[*]}"
	apt-get update -qq >>/var/log/tadris-demos-apt.log 2>&1 || warn "apt-get update reported errors (see /var/log/tadris-demos-apt.log)"
	# Only new packages: stop if apt wants to upgrade anything that is already installed.
	local upgrades
	upgrades="$(apt-get -s install --no-install-recommends "${missing[@]}" | awk '$1 == "Inst" && $3 ~ /^\[/ {print $2}')"
	[[ -z "$upgrades" ]] || die "Installing ${missing[*]} would upgrade installed packages (${upgrades//$'\n'/ }); install them yourself after checking the production sites"
	# Package scripts may not start or restart services while we install.
	local had_policy=0
	[[ -e /usr/sbin/policy-rc.d ]] && had_policy=1
	if [[ $had_policy -eq 0 ]]; then
		printf '#!/bin/sh\nexit 101\n' > /usr/sbin/policy-rc.d
		chmod +x /usr/sbin/policy-rc.d
	fi
	local alt_php; alt_php="$(readlink -f /etc/alternatives/php 2>/dev/null || true)"
	DEBIAN_FRONTEND=noninteractive apt-get install -y -qq --no-install-recommends "${missing[@]}" >>/var/log/tadris-demos-apt.log 2>&1 || {
		[[ $had_policy -eq 0 ]] && rm -f /usr/sbin/policy-rc.d
		die "apt-get install failed (see /var/log/tadris-demos-apt.log)"
	}
	[[ $had_policy -eq 0 ]] && rm -f /usr/sbin/policy-rc.d
	# Keep the server's default `php` (used by the other sites' cron jobs and workers).
	if [[ -n "$alt_php" && "$(readlink -f /etc/alternatives/php)" != "$alt_php" ]]; then
		update-alternatives --set php "$alt_php" >/dev/null
	fi
	# A php-fpm that was not installed before is not used by anything else: keep its default pool off.
	for p in "${missing[@]}"; do
		if [[ "$p" == "php$PHP_VER-fpm" ]] && have_systemd; then
			systemctl disable --now "php$PHP_VER-fpm" >/dev/null 2>&1 || true
		fi
	done
}

install_packages() {
	local pkgs=(curl unzip ca-certificates "php$PHP_VER-fpm" "php$PHP_VER-cli" "php$PHP_VER-mysql" "php$PHP_VER-curl"
		"php$PHP_VER-gd" "php$PHP_VER-intl" "php$PHP_VER-mbstring" "php$PHP_VER-xml" "php$PHP_VER-zip" "php$PHP_VER-bcmath")
	local new_certbot=0
	command -v certbot >/dev/null || { pkgs+=(certbot); new_certbot=1; }
	apt_add_missing "${pkgs[@]}"
	# Package scripts were not allowed to start services: start the renewal timer ourselves.
	[[ $new_certbot -eq 1 ]] && have_systemd && systemctl enable --now certbot.timer >/dev/null 2>&1 || true
	[[ -x "$FPM_BIN" && -x "$PHP_BIN" ]] || die "$FPM_BIN or $PHP_BIN is missing"
}

# ---------------------------------------------------------------------------------------------
# Downloads

SUMS=""
load_sums() {
	[[ -n "$SUMS" || "$SKIP_VERIFY" == 1 ]] && return 0
	local f
	for f in SHA256SUMS.txt SHA256SUMS-package.txt; do
		if [[ -n "$PKG_DIR" && -f "$PKG_DIR/$f" ]]; then
			SUMS+="$(cat "$PKG_DIR/$f")"$'\n'
		else
			SUMS+="$(curl -fsSL --retry 5 "$RELEASE/$f")"$'\n' || die "Could not download $RELEASE/$f"
		fi
	done
}

# Prints the local path of a release asset, downloading it when PKG_DIR does not have it.
get_asset() {
	local name="$1" file
	if [[ -n "$PKG_DIR" && -f "$PKG_DIR/$name" ]]; then
		file="$PKG_DIR/$name"
	else
		mkdir -p "$APP/tmp/dl"
		file="$APP/tmp/dl/$name"
		if [[ ! -f "$file.ok" ]]; then
			log "Downloading $name" >&2
			curl -fL --progress-bar --retry 5 --retry-delay 5 -C - -o "$file" "$RELEASE/$name" >&2 || die "Download failed: $RELEASE/$name"
			touch "$file.ok"
		fi
	fi
	if [[ "$SKIP_VERIFY" != 1 ]]; then
		load_sums
		local want have
		want="$(awk -v n="$name" '$2 == n || $2 == "*"n {print $1}' <<<"$SUMS" | head -1)"
		[[ -n "$want" ]] || die "No checksum for $name"
		have="$(sha256sum "$file" | awk '{print $1}')"
		[[ "$have" == "$want" ]] || { rm -f "$file.ok"; die "Checksum mismatch for $name"; }
	fi
	printf '%s\n' "$file"
}

install_wp_cli() {
	[[ -x "$OPT/bin/wp" ]] && return 0
	mkdir -p "$OPT/bin"
	curl -fsSL --retry 5 -o "$OPT/bin/wp.tmp" "$WP_CLI_URL"
	local want; want="$(curl -fsSL --retry 5 "$WP_CLI_URL.sha512" | awk '{print $1}')"
	[[ "$(sha512sum "$OPT/bin/wp.tmp" | awk '{print $1}')" == "$want" ]] || die "WP-CLI checksum mismatch"
	chmod 0755 "$OPT/bin/wp.tmp" && mv "$OPT/bin/wp.tmp" "$OPT/bin/wp"
}

# ---------------------------------------------------------------------------------------------
# PHP-FPM with ionCube, separate from the system php-fpm services

install_ioncube() {
	[[ "$SKIP_IONCUBE" == 1 ]] && { warn "ionCube skipped: the theme license will not activate"; return 0; }
	local so="$OPT/ioncube/ioncube_loader_lin_$PHP_VER.so"
	if [[ ! -f "$so" ]]; then
		local arch tgz tmp
		case "$(uname -m)" in x86_64) arch=x86-64 ;; aarch64) arch=aarch64 ;; *) die "No ionCube loader for $(uname -m)" ;; esac
		tmp="$(mktemp -d)"
		tgz="${IONCUBE_TGZ:-$tmp/ioncube.tar.gz}"
		if [[ -z "$IONCUBE_TGZ" ]]; then
			log "Downloading the ionCube loader"
			curl -fsSL --retry 5 -o "$tgz" "https://downloads.ioncube.com/loader_downloads/ioncube_loaders_lin_$arch.tar.gz" \
				|| die "Could not download the ionCube loader; download ioncube_loaders_lin_$arch.tar.gz yourself and run with IONCUBE_TGZ=/path/to/it"
		fi
		tar -xzf "$tgz" -C "$tmp"
		[[ -f "$tmp/ioncube/ioncube_loader_lin_$PHP_VER.so" ]] || die "The ionCube package has no loader for PHP $PHP_VER"
		mkdir -p "$OPT/ioncube"
		cp "$tmp/ioncube/ioncube_loader_lin_$PHP_VER.so" "$tmp/ioncube/LICENSE.txt" "$OPT/ioncube/" 2>/dev/null \
			|| cp "$tmp/ioncube/ioncube_loader_lin_$PHP_VER.so" "$OPT/ioncube/"
		rm -rf "$tmp"
	fi
	printf 'zend_extension = %s\n' "$so" > "$ETC/php/pre.d/00-ioncube.ini"
}

setup_php() {
	log "PHP-FPM $PHP_VER for the demos ($UNIT)"
	id "$SYS_USER" >/dev/null 2>&1 || useradd --system --home-dir "$APP" --no-create-home --shell /usr/sbin/nologin "$SYS_USER"
	mkdir -p "$ETC/php/pre.d" "$ETC/php/post.d" "$APP/tmp/sessions" "$APP/logs"
	local ini_src=/usr/lib/php/$PHP_VER/php.ini-production
	[[ -f "$ini_src" ]] || ini_src=/etc/php/$PHP_VER/fpm/php.ini
	[[ -f "$ETC/php/php.ini" ]] || cp "$ini_src" "$ETC/php/php.ini"
	install_ioncube
	cat > "$ETC/php/post.d/99-tadris.ini" <<-INI
		memory_limit = 512M
		upload_max_filesize = 128M
		post_max_size = 128M
		max_execution_time = 300
		max_input_time = 300
		max_input_vars = 5000
		expose_php = Off
		session.save_path = "$APP/tmp/sessions"
		upload_tmp_dir = "$APP/tmp"
		sys_temp_dir = "$APP/tmp"
		error_log = "$APP/logs/php-error.log"
		log_errors = On
		display_errors = Off
	INI
	cat > "$ETC/php-fpm.conf" <<-CONF
		[global]
		pid = $RUN/php-fpm.pid
		error_log = $APP/logs/php-fpm.log

		[tadris]
		user = $SYS_USER
		group = $SYS_USER
		listen = $SOCK
		listen.owner = $NGINX_USER
		listen.group = $NGINX_USER
		listen.mode = 0660
		pm = ondemand
		pm.max_children = 8
		pm.process_idle_timeout = 30s
		pm.max_requests = 500
		php_admin_value[open_basedir] = $APP/:/tmp/:/usr/share/php/
	CONF
	cat > "/etc/systemd/system/$UNIT.service" <<-UNITFILE
		[Unit]
		Description=PHP-FPM for the Akam demos on $DOMAIN
		After=network.target

		[Service]
		Type=notify
		Environment=PHP_INI_SCAN_DIR=$SCAN_DIRS
		ExecStartPre=$FPM_BIN -t -c $ETC/php/php.ini --fpm-config $ETC/php-fpm.conf
		ExecStart=$FPM_BIN --nodaemonize -c $ETC/php/php.ini --fpm-config $ETC/php-fpm.conf
		ExecReload=/bin/kill -USR2 \$MAINPID
		RuntimeDirectory=tadris-php-fpm
		Restart=on-failure

		[Install]
		WantedBy=multi-user.target
	UNITFILE
	chown -R "$SYS_USER:$SYS_USER" "$APP/tmp" "$APP/logs"
	PHP_INI_SCAN_DIR="$SCAN_DIRS" "$FPM_BIN" -t -c "$ETC/php/php.ini" --fpm-config "$ETC/php-fpm.conf" 2>&1 | tail -1
	if [[ "$SKIP_IONCUBE" != 1 ]]; then
		PHP_INI_SCAN_DIR="$SCAN_DIRS" "$PHP_BIN" -c "$ETC/php/php.ini" -v | grep -q 'ionCube' || die "The ionCube loader did not load in PHP $PHP_VER"
	fi
	if have_systemd; then
		systemctl daemon-reload
		systemctl enable "$UNIT" >/dev/null 2>&1
		systemctl restart "$UNIT"
	else
		mkdir -p "$RUN"
		[[ -f "$RUN/php-fpm.pid" ]] && kill "$(cat "$RUN/php-fpm.pid")" 2>/dev/null && sleep 1
		PHP_INI_SCAN_DIR="$SCAN_DIRS" "$FPM_BIN" -D -c "$ETC/php/php.ini" --fpm-config "$ETC/php-fpm.conf" 9>&-
	fi
	for _ in 1 2 3 4 5 6 7 8 9 10; do [[ -S "$SOCK" ]] && break; sleep 1; done
	[[ -S "$SOCK" ]] || die "$UNIT did not start (see $APP/logs/php-fpm.log)"
}

# ---------------------------------------------------------------------------------------------
# nginx

nginx_conf_path() {
	if grep -qE '^\s*include\s+/etc/nginx/sites-enabled/' /etc/nginx/nginx.conf; then
		echo "/etc/nginx/sites-available/$DOMAIN"
	else
		echo "/etc/nginx/conf.d/$DOMAIN.conf"
	fi
}

# The IPv4 address the other sites listen on, if they name one.
listen_ip() {
	local port="$1" cfgs
	cfgs="$(ls /etc/nginx/sites-enabled/* /etc/nginx/conf.d/*.conf 2>/dev/null | grep -v "$DOMAIN" || true)"
	[[ -n "$cfgs" ]] || return 0
	grep -hoE "^\s*listen\s+[0-9]+(\.[0-9]+){3}:$port\b" $cfgs 2>/dev/null | grep -oE '[0-9]+(\.[0-9]+){3}' | head -1 || true
}

# Listen lines that match how the other sites listen (explicit IPv4 address, IPv6).
listen_lines() {
	local port="$1" extra="$2" cfgs ip
	cfgs="$(ls /etc/nginx/sites-enabled/* /etc/nginx/conf.d/*.conf 2>/dev/null | grep -v "$DOMAIN" || true)"
	ip="$(listen_ip "$port")"
	printf '\tlisten %s%s%s;\n' "${ip:+$ip:}" "$port" "$extra"
	if [[ -n "$cfgs" ]] && grep -qE '^\s*listen\s+\[::\]' $cfgs 2>/dev/null; then
		printf '\tlisten [::]:%s%s;\n' "$port" "$extra"
	fi
}

site_locations() {
	cat <<-'NGX'
		root /var/www/tadris/public;
		index index.php index.html;
		client_max_body_size 128M;
		charset utf-8;

		location ^~ /.well-known/acme-challenge/ {
			default_type text/plain;
			try_files $uri =404;
		}
		location ~ /\.(?!well-known) { deny all; }
		location ~* ^/demo-\d+/(?:wp-content/uploads|wp-content/backups-dup-pro|wp-includes)/.*\.php$ { deny all; }
		location ~* ^/demo-\d+/(?:wp-config\.php|readme\.html|license\.txt|xmlrpc\.php)$ { deny all; }
	NGX
	local n s
	for n in 1 2 3 4 5; do
		s="$(slug "$n")"
		printf '\tlocation = /%s { return 301 /%s/; }\n' "$s" "$s"
		printf '\tlocation /%s/ { try_files $uri $uri/ /%s/index.php?$args; }\n' "$s" "$s"
	done
	cat <<-NGX
		location ~* \.(?:css|js|mjs|jpe?g|png|gif|webp|avif|svg|ico|woff2?|ttf|eot|mp4|webm)\$ {
			expires 30d;
			access_log off;
			try_files \$uri =404;
		}
		location ~ \.php\$ {
			try_files \$uri =404;
			fastcgi_split_path_info ^(.+\.php)(/.+)\$;
			include fastcgi_params;
			fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
			fastcgi_param HTTPS \$https if_not_empty;
			fastcgi_pass unix:$SOCK;
			fastcgi_read_timeout 300;
			fastcgi_buffers 16 16k;
			fastcgi_buffer_size 32k;
		}
		access_log $APP/logs/nginx-access.log;
		error_log $APP/logs/nginx-error.log;
	NGX
}

write_nginx() {
	local conf tmp https=0
	conf="$(nginx_conf_path)"
	[[ -f "/etc/letsencrypt/live/$DOMAIN/fullchain.pem" ]] && https=1
	# Nobody else may already serve this name.
	local other
	other="$(grep -rlE "server_name[^;]*\s$DOMAIN(\s|;)" /etc/nginx/sites-enabled /etc/nginx/conf.d 2>/dev/null | while read -r f; do
		[[ "$(realpath "$f")" == "$(realpath -m "$conf")" ]] || echo "$f"; done || true)"
	[[ -z "$other" ]] || die "$DOMAIN is already configured in: $other"

	tmp="$(mktemp)"
	{
		echo "# Akam demos — written by scripts/vps/tadris-demos.sh"
		echo "server {"
		listen_lines 80 ""
		echo "	server_name $DOMAIN;"
		if [[ $https -eq 1 ]]; then
			echo "	location ^~ /.well-known/acme-challenge/ { root $PUB; default_type text/plain; try_files \$uri =404; }"
			echo "	location / { return 301 https://\$host\$request_uri; }"
			echo "}"
			echo "server {"
			listen_lines 443 " ssl"
			echo "	server_name $DOMAIN;"
			echo "	ssl_certificate /etc/letsencrypt/live/$DOMAIN/fullchain.pem;"
			echo "	ssl_certificate_key /etc/letsencrypt/live/$DOMAIN/privkey.pem;"
			echo "	ssl_protocols TLSv1.2 TLSv1.3;"
			echo "	ssl_session_cache shared:tadris_ssl:10m;"
			echo "	add_header Strict-Transport-Security \"max-age=31536000\" always;"
		fi
		site_locations | sed 's/^/\t/; s/^\t\t/\t/'
		echo "}"
	} > "$tmp"

	local backup=""
	[[ -f "$conf" ]] && { backup="$(mktemp)"; cp "$conf" "$backup"; }
	cp "$tmp" "$conf"; rm -f "$tmp"
	if [[ "$conf" == /etc/nginx/sites-available/* ]]; then
		ln -sf "$conf" "/etc/nginx/sites-enabled/$DOMAIN"
	fi
	if ! nginx -t >/dev/null 2>&1; then
		nginx -t || true
		if [[ -n "$backup" ]]; then cp "$backup" "$conf"; else rm -f "$conf" "/etc/nginx/sites-enabled/$DOMAIN"; fi
		die "nginx -t failed with the new site; the previous nginx configuration was kept"
	fi
	[[ -n "$backup" ]] && rm -f "$backup"
	if have_systemd; then systemctl reload nginx; else nginx -s reload 2>/dev/null || nginx 9>&-; fi
	log "nginx site $conf ($( [[ $https -eq 1 ]] && echo https || echo http ))"
}

scheme() { [[ -f "/etc/letsencrypt/live/$DOMAIN/fullchain.pem" ]] && echo https || echo http; }

# ---------------------------------------------------------------------------------------------
# Certificate

obtain_cert() {
	[[ -f "/etc/letsencrypt/live/$DOMAIN/fullchain.pem" ]] && return 0
	command -v certbot >/dev/null || { warn "certbot is not installed"; return 1; }
	# Does DOMAIN reach this nginx? (works behind a CDN proxy too)
	local token; token="$(rand 32)"
	mkdir -p "$PUB/.well-known/acme-challenge"
	echo "$token" > "$PUB/.well-known/acme-challenge/tadris-check"
	local got; got="$(curl -fsS --max-time 15 "http://$DOMAIN/.well-known/acme-challenge/tadris-check" 2>/dev/null || true)"
	rm -f "$PUB/.well-known/acme-challenge/tadris-check"
	if [[ "$got" != "$token" ]]; then
		warn "http://$DOMAIN does not reach this server yet (DNS A record → $(hostname -I | awk '{print $1}')?). The demos run on http for now; run '$0 ssl' after the DNS change."
		return 1
	fi
	log "Requesting a Let's Encrypt certificate for $DOMAIN"
	local mail=(--register-unsafely-without-email)
	[[ -n "$EMAIL" ]] && mail=(-m "$EMAIL")
	certbot certonly --webroot -w "$PUB" -d "$DOMAIN" --cert-name "$DOMAIN" --non-interactive --agree-tos "${mail[@]}" \
		--deploy-hook "systemctl reload nginx" || { warn "certbot failed; staying on http"; return 1; }
}

# ---------------------------------------------------------------------------------------------
# Demos

demo_url() { echo "$(scheme)://$DOMAIN/$(slug "$1")"; }

save_cred() {
	local key="$1" value="$2"
	touch "$CRED"; chmod 600 "$CRED"
	grep -v "^$key=" "$CRED" > "$CRED.tmp" || true
	echo "$key=$value" >> "$CRED.tmp"; mv "$CRED.tmp" "$CRED"; chmod 600 "$CRED"
}
get_cred() { [[ -f "$CRED" ]] && awk -F= -v k="$1" '$1 == k {sub(/^[^=]*=/, ""); print; exit}' "$CRED" || true; }

admin_pass() {
	if [[ -z "${ADMIN_PASS:-}" ]]; then
		ADMIN_PASS="$(get_cred admin_password)"
		[[ -n "$ADMIN_PASS" ]] || ADMIN_PASS="$(rand 20)"
	fi
	save_cred admin_user "$ADMIN_USER"
	save_cred admin_password "$ADMIN_PASS"
}

install_demo() {
	local n="$1" force="${2:-0}" s dir db dbuser dbpass zip work archive sql orig desc
	s="$(slug "$n")"; dir="$PUB/$s"; db="tadris_demo$n"
	guard_path "$dir"
	if [[ -f "$dir/wp-config.php" && "$force" != 1 ]]; then
		log "$s is already installed (use: $0 reinstall $n)"
		return 0
	fi
	log "Installing $s — ${DEMO_TITLE[$n]}"
	zip="$(get_asset "akam-$VERSION-demo-$n.zip")"
	work="$(mktemp -d -p "$APP/tmp")"
	unzip -q -j "$zip" 'demo_*_archive.zip' -d "$work"
	archive="$(ls "$work"/demo_*_archive.zip)"
	rm -rf "${dir:?}"; mkdir -p "$dir"
	unzip -q "$archive" -d "$dir"
	rm -rf "$work"
	sql="$(ls "$dir"/dup-installer/dup_descriptors_*/db_dumps/*.sql)"
	orig="$(ls "$dir"/dup-installer/dup_descriptors_*/orig_files/source_site_wpconfig)"
	desc="$(ls "$dir"/dup-installer/dup_descriptors_*/archive.txt)"
	if grep -q "rtlLicenseFileHash" "$dir/wp-content/themes/akam/functions.php"; then
		die "$s contains an unencoded theme"
	fi

	# Database (the demos' MySQL account, see require_db_account).
	dbuser="$(db_cnf_get user)"; dbpass="$(db_cnf_get password)"
	mysql_db -e "DROP DATABASE IF EXISTS \`$db\`;
		CREATE DATABASE \`$db\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;"
	mysql_db --default-character-set=utf8mb4 "$db" < "$sql"

	local prefix old_url old_path new_url
	prefix="$("$PHP_BIN" -r 'preg_match("/\\\$table_prefix\s*=\s*[\x27\"]([^\x27\"]+)/", file_get_contents($argv[1]), $m); echo $m[1] ?? "wp_";' "$orig")"
	old_url="$(mysql_db -N --default-character-set=utf8mb4 "$db" -e "SELECT option_value FROM \`${prefix}options\` WHERE option_name='siteurl'")"
	old_url="${old_url%/}"
	old_path="$("$PHP_BIN" -r '$d = json_decode(file_get_contents($argv[1]), true); echo rtrim($d["wpInfo"]["targetRoot"] ?? "", "/");' "$desc")"
	new_url="$(demo_url "$n")"

	# wp-config.php from the original site, with this database.
	"$PHP_BIN" -r '
		[$_, $src, $dst, $db, $user, $pass, $host] = $argv;
		$c = file_get_contents($src);
		foreach (["DB_NAME" => $db, "DB_USER" => $user, "DB_PASSWORD" => $pass, "DB_HOST" => $host] as $k => $v) {
			$c = preg_replace("/define\(\s*[\x27\"]" . $k . "[\x27\"]\s*,\s*[\x27\"][^\x27\"]*[\x27\"]\s*\);/", "define( \x27$k\x27, \x27" . addcslashes($v, "\x27\\\\") . "\x27 );", $c, 1);
		}
		$c = preg_replace("/^define\(\s*[\x27\"]DUPLICATOR_AUTH_KEY.*$/m", "", $c);
		$extra = "define( \x27WP_MEMORY_LIMIT\x27, \x27256M\x27 );\ndefine( \x27DISALLOW_FILE_EDIT\x27, true );\ndefine( \x27FS_METHOD\x27, \x27direct\x27 );\n";
		$c = str_replace("/* That\x27s all, stop editing!", $extra . "/* That\x27s all, stop editing!", $c);
		file_put_contents($dst, $c);
	' "$orig" "$dir/wp-config.php" "$db" "$dbuser" "$dbpass" "$DB_HOST"
	grep -q "'DB_NAME', '$db'" "$dir/wp-config.php" || die "Could not write wp-config.php for $s"
	rm -rf "$dir"/dup-installer "$dir"/*_installer-backup.php "$dir"/installer.php "$dir"/wp-content/backups-dup-pro
	# Cache drop-in left by WP Rocket on the original site (the plugin is not in the package).
	if [[ ! -d "$dir/wp-content/plugins/wp-rocket" ]] && grep -qs 'WP_ROCKET' "$dir/wp-content/advanced-cache.php"; then
		rm -rf "$dir/wp-content/advanced-cache.php" "$dir/wp-content/wp-rocket-config" "$dir/wp-content/cache/wp-rocket"
		sed -i "/define( *'WP_CACHE', *true *); *\/\/ Added by WP Rocket/d" "$dir/wp-config.php"
	fi

	install_wp_cli
	wp_cli "$dir" config shuffle-salts --quiet
	local q=(--skip-plugins --skip-themes)
	wp_cli "$dir" "${q[@]}" search-replace "$old_url" "$new_url" --all-tables --precise --quiet
	wp_cli "$dir" "${q[@]}" search-replace "${old_url//\//\\/}" "${new_url//\//\\/}" --all-tables --precise --quiet
	[[ -n "$old_path" && "$old_path" != "$dir" ]] && wp_cli "$dir" "${q[@]}" search-replace "$old_path" "$dir" --all-tables --precise --quiet
	wp_cli "$dir" "${q[@]}" plugin deactivate duplicator-pro --quiet 2>/dev/null || true

	# Admin account.
	if wp_cli "$dir" "${q[@]}" user get "$ADMIN_USER" --field=ID >/dev/null 2>&1; then
		wp_cli "$dir" "${q[@]}" user update "$ADMIN_USER" --user_pass="$ADMIN_PASS" --skip-email --quiet
		wp_cli "$dir" "${q[@]}" user set-role "$ADMIN_USER" administrator --quiet
	else
		local email="$ADMIN_EMAIL"
		wp_cli "$dir" "${q[@]}" user get "$email" --field=ID >/dev/null 2>&1 && email="demo$n-$ADMIN_EMAIL"
		wp_cli "$dir" "${q[@]}" user create "$ADMIN_USER" "$email" --role=administrator --user_pass="$ADMIN_PASS" --quiet
	fi

	wp_cli "$dir" "${q[@]}" transient delete --all --quiet || true
	mysql_db "$db" -e "TRUNCATE \`${prefix}woocommerce_sessions\`;" 2>/dev/null || true
	wp_cli "$dir" elementor flush-css --quiet 2>/dev/null || true
	wp_cli "$dir" rewrite flush --quiet 2>/dev/null || true
	wp_cli "$dir" cache flush --quiet 2>/dev/null || true

	fix_perms "$dir"
	check_demo "$n"
}

fix_perms() {
	chown -R "$SYS_USER:$NGINX_USER" "$1"
	find "$1" -type d -exec chmod 2750 {} +
	find "$1" -type f -exec chmod 0640 {} +
}

check_demo() {
	local n="$1" url code body port=80
	[[ "$(scheme)" == https ]] && port=443
	url="$(demo_url "$n")/"
	body="$(mktemp)"
	code="$(curl -sS --noproxy "*" -o "$body" -w '%{http_code}' --max-time 60 --resolve "$DOMAIN:$port:$(ip="$(listen_ip "$port")"; echo "${ip:-127.0.0.1}")" "$url" || true)"
	if [[ "$code" != 200 ]]; then
		warn "$url answered HTTP $code (see $APP/logs/php-error.log)"
	elif grep -qE 'ionCube Loader for PHP needs to be installed|is corrupted|Fatal error' "$body"; then
		warn "$url shows a PHP error: $(grep -oE 'ionCube Loader for PHP needs to be installed|is corrupted|Fatal error[^<]*' "$body" | head -1)"
	else
		log "$url OK"
	fi
	rm -f "$body"
}

write_landing() {
	local font="" n s cards=""
	for n in 1 2 3 4 5; do
		s="$(slug "$n")"
		[[ -f "$PUB/$s/wp-config.php" ]] || continue
		[[ -z "$font" && -f "$PUB/$s/wp-content/themes/akam/assets/fonts/peyda/woff2/PeydaFaNumWeb-Regular.woff2" ]] \
			&& font="/$s/wp-content/themes/akam/assets/fonts/peyda/woff2/PeydaFaNumWeb"
		cards+="<a class=\"card\" href=\"/$s/\" style=\"--c:${DEMO_COLOR[$n]}\"><span class=\"num\">دمو ${FA_DIGITS[$n]}</span><strong>${DEMO_TITLE[$n]}</strong><span>${DEMO_DESC[$n]}</span><em>مشاهده دمو ←</em></a>"
	done
	cat > "$PUB/index.html" <<-HTML
		<!doctype html>
		<html lang="fa" dir="rtl">
		<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>دموهای پوسته آکام</title>
		<style>
		${font:+@font-face{font-family:peyda;src:url("$font-Regular.woff2") format("woff2");font-weight:400}@font-face{font-family:peyda;src:url("$font-Bold.woff2") format("woff2");font-weight:700}}
		*{box-sizing:border-box}
		body{margin:0;font-family:peyda,Tahoma,sans-serif;background:#f5f7fb;color:#1e293b;line-height:1.8}
		header{text-align:center;padding:64px 16px 32px}
		h1{margin:0 0 8px;font-size:2rem}
		header p{margin:0;color:#64748b}
		main{max-width:1100px;margin:0 auto;padding:0 16px 64px;display:grid;gap:20px;grid-template-columns:repeat(auto-fill,minmax(280px,1fr))}
		.card{display:flex;flex-direction:column;gap:6px;padding:24px;border-radius:18px;background:#fff;text-decoration:none;color:inherit;border-top:5px solid var(--c);box-shadow:0 6px 24px rgba(15,23,42,.06);transition:transform .2s,box-shadow .2s}
		.card:hover{transform:translateY(-4px);box-shadow:0 12px 32px rgba(15,23,42,.12)}
		.num{font-size:.85rem;color:var(--c);font-weight:700}
		.card strong{font-size:1.2rem}
		.card span:not(.num){color:#64748b;font-size:.95rem}
		.card em{margin-top:auto;padding-top:12px;font-style:normal;color:var(--c);font-weight:700}
		</style>
		</head>
		<body>
		<header><h1>دموهای پوسته آکام</h1><p>نسخه $VERSION — پوسته آموزشی و فروشگاهی وردپرس</p></header>
		<main>$cards</main>
		</body>
		</html>
	HTML
	chown "$SYS_USER:$NGINX_USER" "$PUB/index.html"; chmod 0640 "$PUB/index.html"
}

# Bring every installed demo to the scheme nginx serves (http before the certificate, https after).
sync_scheme() {
	local to n dir from
	to="$(scheme)"
	for n in 1 2 3 4 5; do
		dir="$PUB/$(slug "$n")"
		[[ -f "$dir/wp-config.php" ]] || continue
		from="$(wp_cli "$dir" --skip-plugins --skip-themes option get siteurl)"
		from="${from%%://*}"
		[[ "$from" == "$to" ]] && continue
		log "$(slug "$n"): $from → $to"
		wp_cli "$dir" --skip-plugins --skip-themes search-replace "$from://$DOMAIN/" "$to://$DOMAIN/" --all-tables --precise --quiet
		wp_cli "$dir" --skip-plugins --skip-themes search-replace "$from:\\/\\/$DOMAIN\\/" "$to:\\/\\/$DOMAIN\\/" --all-tables --precise --quiet
		wp_cli "$dir" elementor flush-css --quiet 2>/dev/null || true
		wp_cli "$dir" cache flush --quiet 2>/dev/null || true
		fix_perms "$dir/wp-content/uploads"
	done
}

# ---------------------------------------------------------------------------------------------
# Theme update for another WordPress site on this server

update_theme() {
	local path="${1:-}" child=0
	[[ "${2:-}" == --child ]] && child=1
	[[ -n "$path" && -f "$path/wp-config.php" && -d "$path/wp-content/themes" ]] || die "Usage: $0 update-theme /path/to/wordpress [--child]"
	path="$(realpath "$path")"; guard_path "$path"
	local work; work="$(mktemp -d)"
	unzip -q "$(get_asset "akam-$VERSION.zip")" -d "$work"
	[[ -f "$work/akam/style.css" ]] || die "akam-$VERSION.zip has no akam/ folder"
	grep -q "rtlLicenseFileHash" "$work/akam/functions.php" && die "akam-$VERSION.zip is not encoded"
	grep -qE "^\s*Version:\s*$VERSION\s*$" "$work/akam/style.css" || die "akam-$VERSION.zip is not version $VERSION"
	if [[ $child -eq 1 ]]; then
		unzip -q "$(get_asset "akam-child-$VERSION.zip")" -d "$work"
		[[ -f "$work/akam-child/style.css" ]] || die "akam-child-$VERSION.zip has no akam-child/ folder"
	fi
	mkdir -p "$APP/backups"
	local stamp t owner themes="$path/wp-content/themes"
	stamp="$(date +%Y%m%d-%H%M%S)"
	for t in akam $([[ $child -eq 1 ]] && echo akam-child); do
		owner="$(stat -c %U:%G "$themes/$t" 2>/dev/null || stat -c %U:%G "$themes")"
		if [[ -d "$themes/$t" ]]; then
			tar -czf "$APP/backups/$(basename "$path")-$t-$stamp.tar.gz" -C "$themes" "$t"
			mv "$themes/$t" "$themes/.$t-old-$stamp"
		fi
		cp -a "$work/$t" "$themes/$t"
		chown -R "$owner" "$themes/$t"
		rm -rf "$themes/.$t-old-$stamp"
		log "Updated $themes/$t to $VERSION (backup in $APP/backups)"
	done
	rm -rf "$work"
	echo "Regenerate the CSS in WordPress: Elementor → Tools → Regenerate CSS & Data."
}

# ---------------------------------------------------------------------------------------------

status() {
	echo "Domain:   $DOMAIN ($(scheme))"
	echo "PHP-FPM:  $(have_systemd && systemctl is-active "$UNIT" 2>/dev/null || ([[ -S "$SOCK" ]] && echo running || echo stopped))"
	local n
	for n in 1 2 3 4 5; do
		[[ -f "$PUB/$(slug "$n")/wp-config.php" ]] && echo "Demo $n:   $(demo_url "$n")/"
	done
	[[ -f "$CRED" ]] && echo "Admin:    $(get_cred admin_user) (password in $CRED)"
}

uninstall() {
	[[ "${1:-}" == --yes ]] || die "This deletes the demos, their databases and settings. Run: $0 uninstall --yes"
	local conf n; conf="$(nginx_conf_path)"
	rm -f "$conf" "/etc/nginx/sites-enabled/$DOMAIN"
	if nginx -t >/dev/null 2>&1; then
		if have_systemd; then systemctl reload nginx; else nginx -s reload; fi
	fi
	if have_systemd; then
		systemctl disable --now "$UNIT" >/dev/null 2>&1 || true
		rm -f "/etc/systemd/system/$UNIT.service"; systemctl daemon-reload
	elif [[ -f "$RUN/php-fpm.pid" ]]; then
		kill "$(cat "$RUN/php-fpm.pid")" || true
	fi
	for n in 1 2 3 4 5; do
		[[ -f "$DB_CNF" ]] && mysql_db -e "DROP DATABASE IF EXISTS \`tadris_demo$n\`;" 2>/dev/null || true
	done
	rm -rf "$APP" "$ETC" "$OPT" "$CRED" "$DB_CNF"
	id "$SYS_USER" >/dev/null 2>&1 && userdel "$SYS_USER"
	log "Remove the MySQL account yourself if you like: DROP USER '$DB_ACCOUNT'@'localhost', '$DB_ACCOUNT'@'127.0.0.1';"
	log "Removed. The certificate stays in /etc/letsencrypt (certbot delete --cert-name $DOMAIN removes it)."
}

main() {
	local cmd="${1:-install}"; shift || true
	case "$cmd" in
		install)
			preflight
			require_db_account
			install_packages
			mkdir -p "$PUB"; guard_path "$PUB"
			setup_php
			write_nginx
			obtain_cert && write_nginx || true
			admin_pass
			local n; for n in $DEMOS; do install_demo "$n"; done
			rm -rf "$APP/tmp/dl"
			sync_scheme
			write_landing
			echo; status
			;;
		reinstall)
			[[ "${1:-}" =~ ^[1-5]$ ]] || die "Usage: $0 reinstall 1-5"
			preflight; require_db_account; admin_pass; install_demo "$1" 1; write_landing
			;;
		ssl)
			preflight
			obtain_cert || die "No certificate"
			write_nginx
			sync_scheme
			status
			;;
		update-theme) preflight; update_theme "$@" ;;
		status) status ;;
		uninstall) [[ "$(id -u)" -eq 0 ]] || die "Run as root"; uninstall "$@" ;;
		*) sed -n '2,32p' "$0"; exit 1 ;;
	esac
}

main "$@"
