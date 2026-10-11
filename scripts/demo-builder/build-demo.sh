#!/usr/bin/env bash
# build-demo.sh — ساخت محتوای دموهای ۴ (فروش فایل) و ۵ (روانشناسی)
# Generates the images and imports all content, pages, menus and colours into a cloned base site.
#
# Usage: ./build-demo.sh 4|5 [demo-dir]
# Needs: the base site from clone-base.sh, Node.js (Playwright + Chromium) and Python 3 with Pillow.
set -euo pipefail
cd "$(dirname "$0")"
HERE="$PWD"
DEMO="${1:-}"
[[ "$DEMO" == 4 || "$DEMO" == 5 ]] || { echo "Usage: $0 4|5 [demo-dir]" >&2; exit 1; }
DIR="$(realpath -m "${2:-$HERE/../../../akam-demo-$DEMO}")"
WP="php -d memory_limit=1024M $DIR/bin/wp --allow-root --path=$DIR/site"
[ -d node_modules ] || npm install --no-audit --no-fund
# Use a preinstalled Chromium when there is one (e.g. /opt/pw-browsers in Claude Code cloud sessions).
if [ -z "${CHROME_PATH:-}" ]; then
	for c in /opt/pw-browsers/chromium-*/chrome-linux/chrome; do [ -x "$c" ] && export CHROME_PATH="$c"; done
fi

if [ "$DEMO" = 4 ]; then
	# Screenshots of demos 1-3 used on the theme product covers (refresh with tools/vshot.js when the demos change).
	if [ ! -f d4img/src/site4-m.png ]; then
		mkdir -p d4img/src
		cp assets/site-shots/*.png d4img/src/
	fi
	python3 -I d4_data.py
	python3 -I gen_d4.py && node lib/render.js d4img/jobs.json
	python3 -I gen_d4b.py && node lib/render.js d4img/jobs-b.json
	$WP eval-file d4_content.php "$HERE"
	$WP eval-file d4_extra.php "$HERE"
	python3 -I d4_pages.py
	$WP eval-file d4_apply.php "$HERE"
	$WP eval-file palette.php '#3b5bfd' '#2f49e0' '#eaf0ff'
	python3 -I brandfix.py > brandfix.css
	python3 -I -c "import sys; sys.path[:0] = ['$HERE', '$HERE/lib']; from d4_sections import CSS; print(open('brandfix.css').read() + CSS)" > d4-custom.css
	$WP eval-file set_css.php "$HERE/d4-custom.css"
else
	export DEMO5_UPLOADS="$DIR/site/wp-content/uploads"
	python3 -I d5_data.py
	python3 -I gen_d5.py && node lib/render.js d5img/jobs.json
	$WP eval-file d5_content.php "$HERE"
	python3 -I d5_pages.py
	$WP eval-file d5_apply.php "$HERE"
	$WP eval-file palette.php '#0d9488' '#0f766e' '#e0f5f1'
	python3 -I brandfix.py > brandfix.css
	$WP eval-file set_css.php "$HERE/brandfix.css"
fi
$WP elementor flush-css
$WP cache flush
echo "Demo $DEMO built in $DIR"
