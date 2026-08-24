#!/usr/bin/env bash
# Start Learn AI Courses local stack.
# Process:
# 1) Ensure MySQL container is running via docker compose.
# 2) Launch the PHP built-in server on 127.0.0.1:8088.
set -euo pipefail
cd "$(dirname "$0")"
docker compose up -d
echo "MySQL ready on 127.0.0.1:13306"
echo "Site: http://127.0.0.1:8088"
echo "Admin: http://127.0.0.1:8088/wp-admin"
exec php -S 127.0.0.1:8088 router.php
