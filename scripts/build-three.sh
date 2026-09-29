#!/usr/bin/env bash
# Build the 3D background bundle (Three.js + akam/assets/js/src/three-background.js)
# into akam/assets/js/three-background.min.js. Only the Three.js parts imported by
# the source are included. Run after editing the source file.
#
# Usage: ./scripts/build-three.sh
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
WORK="$(mktemp -d)"
trap 'rm -rf "$WORK"' EXIT

cd "$WORK"
npm init -y >/dev/null
npm install --silent three@0.186.1 esbuild@0.28.2

npx esbuild "$ROOT/akam/assets/js/src/three-background.js" \
	--bundle --minify --format=iife --target=es2019 \
	--legal-comments=inline \
	--banner:js="/*! Akam 3D background | three.js r186 (c) 2010-2026 three.js authors, MIT License */" \
	--outfile="$ROOT/akam/assets/js/three-background.min.js" \
	--alias:three="$WORK/node_modules/three"

ls -l "$ROOT/akam/assets/js/three-background.min.js"
