#!/usr/bin/env bash
set -euo pipefail

echo "Running git pull..."
LOCAL_HEAD=$(git rev-parse @)

# Pull updates from remote
git pull

NEW_HEAD=$(git rev-parse @)

if [ "$LOCAL_HEAD" = "$NEW_HEAD" ]; then
        echo "No updates from remote; skipping docker compose steps."
        exit 0
fi

echo "Updates detected — restarting containers."
docker compose up -d --build