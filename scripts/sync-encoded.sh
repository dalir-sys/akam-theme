#!/usr/bin/env bash
# sync-encoded.sh — آرشیو فایل‌های اینکدشده پس از راست‌چین
#
# Usage:
#   ./scripts/sync-encoded.sh 1.0.1 /path/to/rightchin-output
#
# After encoding the 17 PHP files on RightChin (راست‌چین):
#   1. Point SRC at the folder that contains the encoded copies
#      (same relative paths as akam/: functions.php, inc/..., etc.)
#   2. Run this script with VERSION matching style.css / git tag
#   3. Commit encoded/VERSION/ for archive — do NOT overwrite akam/ raw files
#
set -euo pipefail

VERSION="${1:-}"
SRC="${2:-}"

if [[ -z "$VERSION" || -z "$SRC" ]]; then
  echo "Usage: $0 VERSION /path/to/encoded-source" >&2
  echo "Example: $0 1.0.1 ~/Downloads/akam-encoded" >&2
  exit 1
fi

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
DEST="$ROOT/encoded/$VERSION"

FILES=(
  functions.php
  inc/theme-options/ajax-handler.php
  inc/compatibility/elementor.php
  inc/theme-options/admin-page.php
  inc/enqueue.php
  inc/otp-auth.php
  inc/newsletter.php
  inc/contact-form.php
  inc/stories-post-type.php
  inc/teachers-post-type.php
  inc/mega-menu.php
  inc/support-tickets.php
  inc/layout-conditions.php
  inc/layout-renderer.php
  inc/setup.php
  inc/helpers.php
  inc/layout-post-type.php
)

mkdir -p "$DEST"

missing=0
for f in "${FILES[@]}"; do
  if [[ ! -f "$SRC/$f" ]]; then
    echo "MISSING: $SRC/$f" >&2
    missing=1
    continue
  fi
  mkdir -p "$DEST/$(dirname "$f")"
  cp -a "$SRC/$f" "$DEST/$f"
  echo "copied $f -> encoded/$VERSION/$f"
done

if [[ "$missing" -ne 0 ]]; then
  echo "Some files were missing; fix SRC and re-run." >&2
  exit 1
fi

echo "Done. Archive at encoded/$VERSION/"
echo "Next:"
echo "  git add encoded/$VERSION"
echo "  git commit -m \"Archive encoded files for v${VERSION}\""
echo "  git push"
