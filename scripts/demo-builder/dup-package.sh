#!/usr/bin/env bash
# dup-package.sh — ساخت بسته نصب آسان (Duplicator) از کپی آماده‌شده با package-copy.sh
# Builds the Duplicator Pro package of ../../../akam-pkg-N and wraps installer.php + archive like the other demo installers.
#
# Usage: ./dup-package.sh DEMO_NUMBER [version]   → dist/VERSION/akam-VERSION-demo-N.zip
set -euo pipefail
N="$1"; VERSION="${2:-1.5.0}"
HERE="$(cd "$(dirname "$0")" && pwd)"; REPO="$(cd "$HERE/../.." && pwd)"
SITE="$(realpath "$REPO/../akam-pkg-$N")"; OUT="$(mktemp -d)"
trap 'rm -rf "$OUT"' EXIT

AKAM_DUP_NAME="demo_$N" php -d memory_limit=2048M -d max_execution_time=0 "$SITE/bin/wp" --allow-root --path="$SITE/site" \
	--require="$HERE/dup-name.php" duplicator build --dir="$OUT" --delete
ARCHIVE="$(ls "$OUT"/demo_"$N"_*_archive.zip)"
cp "$OUT"/demo_"$N"_*_installer.php* "$OUT/installer.php"

# The theme inside must be the encoded build.
if unzip -p "$ARCHIVE" wp-content/themes/akam/functions.php | grep -q "rtlLicenseFileHash"; then
	echo "The packaged theme is not encoded" >&2; exit 1
fi

mkdir -p "$REPO/dist/$VERSION"
rm -f "$REPO/dist/$VERSION/akam-$VERSION-demo-$N.zip"
(cd "$OUT" && zip -q -0 -X "$REPO/dist/$VERSION/akam-$VERSION-demo-$N.zip" installer.php "$(basename "$ARCHIVE")")
ls -l "$REPO/dist/$VERSION/akam-$VERSION-demo-$N.zip"
