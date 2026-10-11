#!/usr/bin/env bash
# build-release.sh — ساخت بسته‌های نهایی (اینکدشده) یک نسخه
#
# Usage:
#   ./scripts/build-release.sh 1.5.0 [out-dir]
#
# Takes akam/ and akam-child/ from the committed HEAD, replaces the encoded files with
# encoded/VERSION/ (see sync-encoded.sh) and checks that the RTL license file matches the
# hash in the raw functions.php. Writes to out-dir (default: dist/VERSION):
#   akam-VERSION.zip            parent theme (installable; also the update file)
#   akam-child-VERSION.zip      child theme
#   akam-VERSION-update.zip     update file for Rtl-Theme (same as the parent theme zip)
#   akam-VERSION-package.zip    product package: both themes + CHANGELOG.md
#   akam/, akam-child/          unpacked encoded themes (used to rebuild the demo installers)
set -euo pipefail

VERSION="${1:-}"
if [[ -z "$VERSION" ]]; then
	echo "Usage: $0 VERSION [out-dir]" >&2
	exit 1
fi

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
OUT="$(realpath -m "${2:-$ROOT/dist/$VERSION}")"
ENC="$ROOT/encoded/$VERSION"

[[ -d "$ENC" ]] || { echo "Missing $ENC (run sync-encoded.sh first)" >&2; exit 1; }

STYLE_VERSION="$(git -C "$ROOT" show HEAD:akam/style.css | sed -n -E 's/^Version:[[:space:]]*//p')"
if [[ "$STYLE_VERSION" != "$VERSION" ]]; then
	echo "akam/style.css at HEAD is $STYLE_VERSION, not $VERSION" >&2
	exit 1
fi

rm -rf "$OUT" && mkdir -p "$OUT"
git -C "$ROOT" archive HEAD akam akam-child | tar -x -C "$OUT"

# License hash must match the committed license file, otherwise the theme never activates.
HASH="$(sed -n -E "s/.*rtlLicenseFileHash === '([0-9a-f]{40})'.*/\1/p" "$OUT/akam/functions.php")"
FILE_HASH="$(sha1sum "$OUT/akam/RTL_License_1caedf22ac6ba764.php" | cut -d' ' -f1)"
if [[ -z "$HASH" || "$HASH" != "$FILE_HASH" ]]; then
	echo "License hash mismatch: functions.php=$HASH license file=$FILE_HASH" >&2
	exit 1
fi

# Every encoded file must replace an existing raw file.
(cd "$ENC" && find . -type f -name '*.php') | while read -r f; do
	[[ -f "$OUT/akam/$f" ]] || { echo "Encoded file has no raw counterpart: $f" >&2; exit 1; }
	cp "$ENC/$f" "$OUT/akam/$f"
done

# Encoded files must not be raw any more (raw functions.php still contains the hash string).
if grep -q "rtlLicenseFileHash" "$OUT/akam/functions.php"; then
	echo "functions.php in $ENC is not encoded" >&2
	exit 1
fi

find "$OUT/akam" "$OUT/akam-child" -name '*.php' -print0 | xargs -0 -n1 php -l >/dev/null

(cd "$OUT" && zip -q -r -X "akam-$VERSION.zip" akam && zip -q -r -X "akam-child-$VERSION.zip" akam-child)
cp "$OUT/akam-$VERSION.zip" "$OUT/akam-$VERSION-update.zip"
(cd "$OUT" && cp "$ROOT/CHANGELOG.md" . && zip -q -X "akam-$VERSION-package.zip" "akam-$VERSION.zip" "akam-child-$VERSION.zip" CHANGELOG.md && rm CHANGELOG.md)

echo "License hash: $HASH"
ls -l "$OUT"/*.zip
