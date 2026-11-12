#!/bin/sh
set -eu

api="${VITE_API_URL:-http://localhost:41808/api}"

case "$api" in
  *localhost*|*127.0.0.1*)
    echo "VITE_API_URL=$api"
    echo "Browsers will call that address. On Coolify, set VITE_API_URL to the public API URL, including /api."
    ;;
esac

escaped=$(printf '%s' "$api" | sed 's/\\/\\\\/g; s/"/\\"/g; s/</\\u003c/g')
printf 'window.__SMART_MINING_API__="%s";\n' "$escaped" > /usr/share/nginx/html/config.js
