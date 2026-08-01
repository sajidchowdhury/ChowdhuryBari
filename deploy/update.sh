#!/usr/bin/env bash
# ============================================================
# ChowdhuryBari — Safe Update Script
# ============================================================
# Lightweight update for routine code changes (no downtime).
# Uses artisan down/up for zero-downtime switchover.
# Idempotent: safe to run multiple times.
#
# Usage:
#   ./deploy/update.sh                # Update from current branch
#   ./deploy/update.sh --skip-migrate # Skip migrations
#   ./deploy/update.sh --skip-npm     # Skip frontend rebuild
# ============================================================

set -euo pipefail

# ----- Color Output -----
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

info()  { echo -e "${BLUE}[INFO]${NC}  $*"; }
ok()    { echo -e "${GREEN}[OK]${NC}    $*"; }
warn()  { echo -e "${YELLOW}[WARN]${NC}  $*"; }
error() { echo -e "${RED}[ERROR]${NC} $*"; exit 1; }

# ----- Parse Arguments -----
SKIP_NPM=false
SKIP_MIGRATE=false

for arg in "$@"; do
    case $arg in
        --skip-npm)      SKIP_NPM=true ;;
        --skip-migrate)  SKIP_MIGRATE=true ;;
        --help|-h)
            echo "Usage: $0 [--skip-npm] [--skip-migrate]"
            exit 0
            ;;
        *) error "Unknown argument: $arg" ;;
    esac
done

# ----- Configuration -----
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"

# Detect binaries
PHP_PATH=$(which php 2>/dev/null || echo "/usr/local/bin/php")
COMPOSER_PATH=$(which composer 2>/dev/null || echo "/usr/local/bin/composer")
NODE_PATH=$(which node 2>/dev/null || echo "/usr/local/bin/node")
NPM_PATH=$(which npm 2>/dev/null || echo "/usr/local/bin/npm")

BRANCH=$(cd "$PROJECT_ROOT" && git rev-parse --abbrev-ref HEAD 2>/dev/null || echo "main")

info "=== ChowdhuryBari Update ==="
info "Branch: $BRANCH"

cd "$PROJECT_ROOT"

# ----- Quick Health Check -----
[ -f "$PROJECT_ROOT/.env" ] || error ".env file not found. Run deploy.sh first."
[ -x "$PHP_PATH" ] || error "PHP not found at $PHP_PATH"

# ----- Maintenance Mode (brief) -----
info "Enabling maintenance mode..."
"$PHP_PATH" artisan down --message="Updating... back in a moment." --retry=30

# ----- Pull Code -----
info "Pulling latest code..."
git fetch origin "$BRANCH"
LOCAL_HASH=$(git rev-parse HEAD)
REMOTE_HASH=$(git rev-parse "origin/$BRANCH" 2>/dev/null || echo "")

if [ "$LOCAL_HASH" = "$REMOTE_HASH" ]; then
    info "Already up to date. No code changes."
    "$PHP_PATH" artisan up
    ok "No update needed."
    exit 0
fi

git reset --hard "origin/$BRANCH"
ok "Code updated."

# ----- Composer -----
info "Updating Composer dependencies..."
"$COMPOSER_PATH" install --no-dev --prefer-dist --no-interaction --optimize-autoloader --no-progress
ok "Composer updated."

# ----- NPM & Vite -----
if [ "$SKIP_NPM" = false ] && [ -x "$NPM_PATH" ]; then
    info "Rebuilding frontend assets..."
    "$NPM_PATH" install --no-audit --no-fund
    "$NPM_PATH" run build
    ok "Frontend assets rebuilt."
else
    info "Skipping frontend build."
fi

# ----- Migrations -----
if [ "$SKIP_MIGRATE" = false ]; then
    info "Running pending migrations..."
    "$PHP_PATH" artisan migrate --force --no-interaction
    ok "Migrations done."
else
    info "Skipping migrations."
fi

# ----- Optimize Caches -----
info "Optimizing caches..."
"$PHP_PATH" artisan config:clear
"$PHP_PATH" artisan route:clear
"$PHP_PATH" artisan view:clear
"$PHP_PATH" artisan event:clear
"$PHP_PATH" artisan cache:clear

"$PHP_PATH" artisan config:cache
"$PHP_PATH" artisan route:cache
"$PHP_PATH" artisan view:cache
"$PHP_PATH" artisan event:cache
ok "Caches optimized."

# ----- Storage Symlink -----
info "Ensuring storage symlink..."
if [ -L "$PROJECT_ROOT/public/storage" ]; then
    ok "Storage symlink already exists."
else
    "$PHP_PATH" artisan storage:link
    ok "Storage symlink created."
fi

# ----- Permissions -----
info "Setting permissions..."
chmod -R 775 "$PROJECT_ROOT/storage" 2>/dev/null || true
chmod -R 775 "$PROJECT_ROOT/bootstrap/cache" 2>/dev/null || true
ok "Permissions set."

# ----- Back Online -----
"$PHP_PATH" artisan up

DEPLOY_TIMESTAMP=$(date +"%Y-%m-%d %H:%M:%S")
DEPLOY_COMMIT=$(git rev-parse --short HEAD 2>/dev/null || echo "unknown")
echo "$DEPLOY_TIMESTAMP | $BRANCH | $DEPLOY_COMMIT | update" >> "$PROJECT_ROOT/deploy/last_release.txt"

ok "=== Update Complete ==="
ok "Commit: $DEPLOY_COMMIT at $DEPLOY_TIMESTAMP"
