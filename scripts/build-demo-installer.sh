#!/usr/bin/env bash
# build-demo-installer.sh — به‌روزرسانی قالب داخل بسته نصبی (Duplicator) دموها
#
# Usage:
#   ./scripts/build-demo-installer.sh VERSION DEMO_NUMBER DUPLICATOR_ARCHIVE [out-dir]
#
# DUPLICATOR_ARCHIVE is the inner demo_<n>_*_archive.zip (the one setup-demo.sh keeps as
# <demo-dir>/pkg/archive.zip). Run build-release.sh VERSION first: the encoded akam/ and
# akam-child/ are taken from dist/VERSION.
#
# The site, database and plugins stay exactly as packaged; only wp-content/themes/akam and
# wp-content/themes/akam-child are replaced. Writes out-dir/akam-VERSION-demo-<n>.zip with
# installer.php and the archive (its name is kept: the installer checks the package hash in it).
set -euo pipefail

VERSION="${1:-}" DEMO="${2:-}" ARCHIVE="${3:-}"
if [[ -z "$VERSION" || -z "$DEMO" || ! -f "$ARCHIVE" ]]; then
	echo "Usage: $0 VERSION DEMO_NUMBER DUPLICATOR_ARCHIVE [out-dir]" >&2
	exit 1
fi

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
DIST="$ROOT/dist/$VERSION"
OUT="$(realpath -m "${4:-$DIST}")"
[[ -d "$DIST/akam" && -d "$DIST/akam-child" ]] || { echo "Run build-release.sh $VERSION first" >&2; exit 1; }

ARCHIVE="$(realpath "$ARCHIVE")"
NAME="$(unzip -Z1 "$ARCHIVE" | grep -E '^demo_[0-9]+_[0-9a-f]+_[0-9]+_installer-backup\.php$' | head -1)"
[[ -n "$NAME" ]] || { echo "No Duplicator installer backup in $ARCHIVE" >&2; exit 1; }
ARCHIVE_NAME="${NAME%_installer-backup.php}_archive.zip"

WORK="$(mktemp -d)"
trap 'rm -rf "$WORK"' EXIT

cp "$ARCHIVE" "$WORK/$ARCHIVE_NAME"
zip -q -d "$WORK/$ARCHIVE_NAME" 'wp-content/themes/akam/*' 'wp-content/themes/akam-child/*'
mkdir -p "$WORK/stage/wp-content/themes"
cp -a "$DIST/akam" "$DIST/akam-child" "$WORK/stage/wp-content/themes/"
(cd "$WORK/stage" && zip -q -r -X "$WORK/$ARCHIVE_NAME" wp-content)

# installer.php = the backup copy inside the archive, with the new archive size.
unzip -p "$ARCHIVE" "$NAME" > "$WORK/installer.php"
SIZE="$(stat -c %s "$WORK/$ARCHIVE_NAME")"
sed -i -E "s/(const ARCHIVE_SIZE[[:space:]]*= ')[0-9]+(';)/\1$SIZE\2/" "$WORK/installer.php"
grep -q "ARCHIVE_SIZE           = '$SIZE'" "$WORK/installer.php" || { echo "Could not set ARCHIVE_SIZE" >&2; exit 1; }
php -l "$WORK/installer.php" >/dev/null

mkdir -p "$OUT"
rm -f "$OUT/akam-$VERSION-demo-$DEMO.zip"
(cd "$WORK" && zip -q -0 -X "$OUT/akam-$VERSION-demo-$DEMO.zip" installer.php "$ARCHIVE_NAME")
ls -l "$OUT/akam-$VERSION-demo-$DEMO.zip"
