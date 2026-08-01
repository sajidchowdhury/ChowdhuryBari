#!/usr/bin/env bash
# ============================================================
# ChowdhuryBari — Rollback Script
# ============================================================
# Rolls back to the previous Git commit and restores the
# application state. Safe and idempotent.
#
# Usage:
#   ./deploy/rollback.sh              # Roll back 1 commit
#   ./deploy/rollback.sh --steps=3    # Roll back 3 commits
#   ./deploy/rollback.sh --to=abc123  # Roll back to specific commit
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
STEPS=1
TARGET_COMMIT=""

for arg in "$@"; do
    case $arg in
        --steps=*)   STEPS="${arg#*=}" ;;
        --to=*)      TARGET_COMMIT="${arg#*=}" ;;
        --help|-h)
            echo "Usage: $0 [--steps=N] [--to=COMMIT_HASH]"
            echo "  --steps=N   Roll back N commits (default: 1)"
            echo "  --to=HASH   Roll back to specific commit hash"
            exit 0
            ;;
        *) error "Unknown argument: $arg" ;;
    esac
done

# ----- Configuration -----
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"

PHP_PATH=$(which php 2>/dev/null || echo "/usr/local/bin/php")
COMPOSER_PATH=$(which composer 2>/dev/null || echo "/usr/local/bin/composer")

cd "$PROJECT_ROOT"

# ----- Pre-flight -----
[ -f "$PROJECT_ROOT/.env" ] || error ".env not found. Nothing to roll back."
[ -x "$PHP_PATH" ] || error "PHP not found."

CURRENT_COMMIT=$(git rev-parse --short HEAD)
CURRENT_BRANCH=$(git rev-parse --abbrev-ref HEAD 2>/dev/null || echo "unknown")

info "=== ChowdhuryBari Rollback ==="
info "Current commit : $CURRENT_COMMIT"
info "Current branch : $CURRENT_BRANCH"

# ----- Determine Target -----
if [ -n "$TARGET_COMMIT" ]; then
    # Validate the commit exists
    if ! git rev-parse --verify "$TARGET_COMMIT" >/dev/null 2>&1; then
        error "Commit $TARGET_COMMIT not found in repository."
    fi
    ROLLBACK_TARGET="$TARGET_COMMIT"
    info "Rolling back to specific commit: $ROLLBACK_TARGET"
else
    ROLLBACK_TARGET="HEAD~$STEPS"
    # Validate we can go back that many steps
    if ! git rev-parse --verify "$ROLLBACK_TARGET" >/dev/null 2>&1; then
        error "Cannot roll back $STEPS commits. Not enough history."
    fi
    info "Rolling back $STEPS commit(s)..."
fi

ROLLBACK_SHORT=$(git rev-parse --short "$ROLLBACK_TARGET")

if [ "$CURRENT_COMMIT" = "$ROLLBACK_SHORT" ]; then
    warn "Already at target commit $ROLLBACK_SHORT. Nothing to do."
    exit 0
fi

# ----- Maintenance Mode -----
info "Enabling maintenance mode..."
"$PHP_PATH" artisan down --message="Rolling back to a stable version. Please wait." --retry=60

# ----- Reset Code -----
info "Rolling back code from $CURRENT_COMMIT to $ROLLBACK_SHORT..."

# Use a hard reset to the target commit
git reset --hard "$ROLLBACK_TARGET"
ok "Code rolled back to $ROLLBACK_SHORT."

# ----- Restore Dependencies -----
info "Reinstalling Composer dependencies for rolled-back version..."
"$COMPOSER_PATH" install --no-dev --prefer-dist --no-interaction --optimize-autoloader --no-progress
ok "Composer dependencies restored."

# ----- Rebuild Assets -----
NPM_PATH=$(which npm 2>/dev/null || echo "")
if [ -n "$NPM_PATH" ] && [ -x "$NPM_PATH" ]; then
    info "Rebuilding frontend assets..."
    "$NPM_PATH" install --no-audit --no-fund
    "$NPM_PATH" run build
    ok "Frontend assets rebuilt."
else
    warn "NPM not found. Skipping frontend rebuild."
    warn "Assets from the rolled-back version's last build will be used."
fi

# ----- Roll Back Migrations -----
# We do NOT automatically rollback migrations because:
# 1. The previous commit's code should handle the current schema
# 2. Rolling back migrations can cause DATA LOSS
# 3. If a migration was the problem, handle it manually
warn "Note: Database migrations were NOT rolled back automatically."
warn "If the issue was migration-related, run manually:"
warn "  $PHP_PATH artisan migrate:rollback --step=1"

# ----- Optimize Caches -----
info "Clearing and rebuilding caches..."
"$PHP_PATH" artisan config:clear
"$PHP_PATH" artisan route:clear
"$PHP_PATH" artisan view:clear
"$PHP_PATH" artisan event:clear
"$PHP_PATH" artisan cache:clear

"$PHP_PATH" artisan config:cache
"$PHP_PATH" artisan route:cache
"$PHP_PATH" artisan view:cache
"$PHP_PATH" artisan event:cache
ok "Caches rebuilt."

# ----- Storage Symlink -----
info "Ensuring storage symlink..."
if [ ! -L "$PROJECT_ROOT/public/storage" ]; then
    "$PHP_PATH" artisan storage:link
fi
ok "Storage symlink ready."

# ----- Permissions -----
chmod -R 775 "$PROJECT_ROOT/storage" 2>/dev/null || true
chmod -R 775 "$PROJECT_ROOT/bootstrap/cache" 2>/dev/null || true

# ----- Back Online -----
"$PHP_PATH" artisan up

DEPLOY_TIMESTAMP=$(date +"%Y-%m-%d %H:%M:%S")
NEW_COMMIT=$(git rev-parse --short HEAD)
echo "$DEPLOY_TIMESTAMP | $CURRENT_BRANCH | $NEW_COMMIT | rollback-from-$CURRENT_COMMIT" >> "$PROJECT_ROOT/deploy/last_release.txt"

ok "=== Rollback COMPLETE ==="
ok "Rolled back from : $CURRENT_COMMIT"
ok "Now at commit     : $NEW_COMMIT"
ok "Timestamp         : $DEPLOY_TIMESTAMP"
warn "If the rollback didn't resolve the issue, consider:"
warn "  1. Rolling back further: ./deploy/rollback.sh --steps=2"
warn "  2. Rolling back to a known good commit: ./deploy/rollback.sh --to=<hash>"
warn "  3. Checking deployment log: cat deploy/last_release.txt"
