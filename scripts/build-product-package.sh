#!/usr/bin/env bash
# build-product-package.sh — بسته کامل محصول: easyInstaller، plugins، theme و help.pdf
#
# Usage: ./scripts/build-product-package.sh VERSION PLUGINS_DIR [help.pdf]
#   dist/VERSION must contain akam-VERSION.zip, akam-child-VERSION.zip and akam-VERSION-demo-1..5.zip
#   PLUGINS_DIR holds the Elementor and WooCommerce zips (latest stable from wordpress.org).
# Writes dist/VERSION/akam-VERSION-full-package.zip and refuses to build if any theme copy is not encoded.
set -euo pipefail
VERSION="${1:-}"; PLUGINS="${2:-}"
[[ -n "$VERSION" && -d "$PLUGINS" ]] || { echo "Usage: $0 VERSION PLUGINS_DIR [help.pdf]" >&2; exit 1; }
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
DIST="$ROOT/dist/$VERSION"
HELP="${3:-$DIST/help.pdf}"
STAGE="$DIST/package"

ls "$PLUGINS"/elementor*.zip "$PLUGINS"/woocommerce*.zip >/dev/null
[ -f "$HELP" ] || { echo "Missing $HELP (render package/help/build-help.js)" >&2; exit 1; }

rm -rf "$STAGE" && mkdir -p "$STAGE/easyInstaller" "$STAGE/plugins" "$STAGE/theme"
for n in 1 2 3 4 5; do cp "$DIST/akam-$VERSION-demo-$n.zip" "$STAGE/easyInstaller/"; done
cp "$PLUGINS"/elementor*.zip "$PLUGINS"/woocommerce*.zip "$STAGE/plugins/"
cp "$DIST/akam-$VERSION.zip" "$DIST/akam-child-$VERSION.zip" "$STAGE/theme/"
cp "$HELP" "$STAGE/help.pdf"

# Only the encoded theme may ship: check the theme zip and the theme inside every demo archive.
python3 -I - "$STAGE" <<'PY'
import sys, zipfile, glob, io
stage = sys.argv[1]
def check(z, where):
    names = [n for n in z.namelist() if n.endswith('themes/akam/functions.php') or n == 'akam/functions.php']
    if not names: sys.exit(f'no akam/functions.php in {where}')
    for n in names:
        if b'rtlLicenseFileHash' in z.read(n): sys.exit(f'raw (unencoded) theme in {where}: {n}')
for f in glob.glob(stage + '/theme/akam-[0-9]*.zip'):
    check(zipfile.ZipFile(f), f)
for f in sorted(glob.glob(stage + '/easyInstaller/*.zip')):
    outer = zipfile.ZipFile(f)
    inner = [n for n in outer.namelist() if n.endswith('_archive.zip')]
    if len(inner) != 1 or 'installer.php' not in outer.namelist(): sys.exit(f'bad installer package {f}')
    with outer.open(inner[0]) as fh:
        check(zipfile.ZipFile(io.BytesIO(fh.read())), f)
    print('encoded theme ok:', f.rsplit('/', 1)[1])
PY

OUT="$DIST/akam-$VERSION-full-package.zip"
rm -f "$OUT"
(cd "$STAGE" && zip -q -0 -r -X "$OUT" easyInstaller plugins theme help.pdf)
unzip -l "$OUT"
