#!/usr/bin/env bash
# bump-version.sh — هم‌تراز کردن Version در style.css و ثابت تم
#
# Usage:
#   ./scripts/bump-version.sh 1.0.1
#
set -euo pipefail

VERSION="${1:-}"
if [[ -z "$VERSION" ]]; then
  echo "Usage: $0 X.Y.Z" >&2
  exit 1
fi

if [[ ! "$VERSION" =~ ^[0-9]+\.[0-9]+\.[0-9]+([.-][A-Za-z0-9.]+)?$ ]]; then
  echo "Invalid version: $VERSION" >&2
  exit 1
fi

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
PARENT_CSS="$ROOT/akam/style.css"
CHILD_CSS="$ROOT/akam-child/style.css"
FUNCTIONS="$ROOT/akam/functions.php"

if [[ ! -f "$PARENT_CSS" ]]; then
  echo "Missing $PARENT_CSS" >&2
  exit 1
fi

# Update Version: in style.css (parent)
sed -i -E "s/^(Version:[[:space:]]*).*/\1${VERSION}/" "$PARENT_CSS"
echo "Updated Version in akam/style.css -> $VERSION"

if [[ -f "$CHILD_CSS" ]]; then
  sed -i -E "s/^(Version:[[:space:]]*).*/\1${VERSION}/" "$CHILD_CSS"
  echo "Updated Version in akam-child/style.css -> $VERSION"
fi

if [[ -f "$FUNCTIONS" ]] && grep -q "WEBMZ_VERSION" "$FUNCTIONS"; then
  # define( 'WEBMZ_VERSION', '1.0.0' ); or simila
  sed -i -E "s/(WEBMZ_VERSION['\"]?\s*,\s*['\"])[^'\"]+(['\"])/\1${VERSION}\2/" "$FUNCTIONS"
  echo "Updated WEBMZ_VERSION in akam/functions.php -> $VERSION"
else
  echo "WEBMZ_VERSION not found in functions.php (skipped)"
fi

echo ""
echo "Reminder: after commit, create and push tag:"
echo "  git tag v${VERSION}"
echo "  git push origin v${VERSION}"
