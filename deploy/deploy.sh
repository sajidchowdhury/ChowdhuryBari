#!/usr/bin/env bash
# ============================================================
# ChowdhuryBari — Production Deployment Script
# ============================================================
# Idempotent: safe to run multiple times.
# Designed for cPanel/WHM shared hosting with SSH + Git.
#
# Usage:
#   ./deploy/deploy.sh              # Deploy current branch
#   ./deploy/deploy.sh --branch=staging  # Deploy specific branch
#   ./deploy/deploy.sh --skip-npm       # Skip npm build step
#   ./deploy/deploy.sh --skip-migrate   # Skip database migration
# ============================================================

set -euo pipefail

# ----- Color Output -----
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

info()  { echo -e "${BLUE}[INFO]${NC}  $*"; }
ok()    { echo -e "${GREEN}[OK]${NC}    $*"; }
warn()  { echo -e "${YELLOW}[WARN]${NC}  $*"; }
error() { echo -e "${RED}[ERROR]${NC} $*"; exit 1; }

# ----- Parse Arguments -----
BRANCH=""
SKIP_NPM=false
SKIP_MIGRATE=false
DRY_RUN=false

for arg in "$@"; do
    case $arg in
        --branch=*)  BRANCH="${arg#*=}" ;;
        --skip-npm)  SKIP_NPM=true ;;
        --skip-migrate) SKIP_MIGRATE=true ;;
        --dry-run)   DRY_RUN=true ;;
        --help|-h)
            echo "Usage: $0 [--branch=NAME] [--skip-npm] [--skip-migrate] [--dry-run]"
            exit 0
            ;;
        *) error "Unknown argument: $arg. Use --help for usage." ;;
    esac
done

# ----- Configuration -----
# Paths — auto-detect from script location
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"

# Override paths from .env if available
if [ -f "$PROJECT_ROOT/.env" ]; then
    # Source deploy variables from .env
    DEPLOY_BRANCH=$(grep -E '^DEPLOY_BRANCH=' "$PROJECT_ROOT/.env" 2>/dev/null | cut -d'=' -f2- || echo "")
    PHP_PATH=$(grep -E '^DEPLOY_PHP_PATH=' "$PROJECT_ROOT/.env" 2>/dev/null | cut -d'=' -f2- || echo "")
    COMPOSER_PATH=$(grep -E '^DEPLOY_COMPOSER_PATH=' "$PROJECT_ROOT/.env" 2>/dev/null | cut -d'=' -f2- || echo "")
    NODE_PATH=$(grep -E '^DEPLOY_NODE_PATH=' "$PROJECT_ROOT/.env" 2>/dev/null | cut -d'=' -f2- || echo "")
    NPM_PATH=$(grep -E '^DEPLOY_NPM_PATH=' "$PROJECT_ROOT/.env" 2>/dev/null | cut -d'=' -f2- || echo "")
fi

# Use provided branch or detected branch or current branch
: "${BRANCH:="${DEPLOY_BRANCH:-}"}"
: "${BRANCH:=$(cd "$PROJECT_ROOT" && git rev-parse --abbrev-ref HEAD 2>/dev/null || echo "main")}"

# Binary paths — fall back to system defaults
: "${PHP_PATH:=$(which php 2>/dev/null || echo "/usr/local/bin/php")}"
: "${COMPOSER_PATH:=$(which composer 2>/dev/null || echo "/usr/local/bin/composer")}"
: "${NODE_PATH:=$(which node 2>/dev/null || echo "/usr/local/bin/node")}"
: "${NPM_PATH:=$(which npm 2>/dev/null || echo "/usr/local/bin/npm")}"

# ----- Pre-flight Checks -----
info "========================================="
info "ChowdhuryBari Production Deployment"
info "========================================="
info "Project root : $PROJECT_ROOT"
info "Branch       : $BRANCH"
info "PHP          : $PHP_PATH"
info "Composer     : $COMPOSER_PATH"
info "Node         : $NODE_PATH"
info "NPM          : $NPM_PATH"
info "========================================="

# Verify we're in a Laravel project
[ -f "$PROJECT_ROOT/artisan" ] || error "artisan not found. Is this a Laravel project?"
[ -f "$PROJECT_ROOT/composer.json" ] || error "composer.json not found."

# Verify binaries exist
[ -x "$PHP_PATH" ] || error "PHP not found at $PHP_PATH. Set DEPLOY_PHP_PATH in .env."
[ -x "$COMPOSER_PATH" ] || error "Composer not found at $COMPOSER_PATH. Set DEPLOY_COMPOSER_PATH in .env."

# Check .env file exists
if [ ! -f "$PROJECT_ROOT/.env" ]; then
    warn ".env file not found. Copying from .env.example..."
    if [ -f "$PROJECT_ROOT/.env.example" ]; then
        cp "$PROJECT_ROOT/.env.example" "$PROJECT_ROOT/.env"
        warn "IMPORTANT: Edit .env with your production values before re-running deploy."
        warn "At minimum, set APP_KEY, DB_*, and APP_URL."
        exit 1
    else
        error "Neither .env nor .env.example found. Cannot proceed."
    fi
fi

# Check APP_KEY is set
APP_KEY=$("$PHP_PATH" "$PROJECT_ROOT/artisan" tinker --execute="echo config('app.key');" 2>/dev/null || echo "")
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    warn "APP_KEY is empty. Generating..."
    "$PHP_PATH" "$PROJECT_ROOT/artisan" key:generate --force
fi

if [ "$DRY_RUN" = true ]; then
    info "Dry run mode — no changes will be made."
    info "All pre-flight checks passed."
    exit 0
fi

# ----- Step 1: Enter Maintenance Mode -----
info "Step 1/10: Enabling maintenance mode..."
"$PHP_PATH" "$PROJECT_ROOT/artisan" down --message="We are updating the application. Please check back in a minute." --retry=60

# ----- Step 2: Pull Latest Code -----
info "Step 2/10: Pulling latest code from $BRANCH..."
cd "$PROJECT_ROOT"

# Stash any local changes (e.g., runtime config)
STASHED=false
if ! git diff --quiet 2>/dev/null || ! git diff --cached --quiet 2>/dev/null; then
    warn "Uncommitted changes detected. Stashing..."
    git stash push -m "deploy-auto-stash-$(date +%Y%m%d%H%M%S)"
    STASHED=true
fi

git fetch origin "$BRANCH"
git checkout "$BRANCH"
git reset --hard "origin/$BRANCH"

# Restore stashed changes if any
if [ "$STASHED" = true ]; then
    info "Restoring stashed changes..."
    git stash pop || warn "Could not restore stash. Manual resolution may be needed."
fi

ok "Code updated to latest $BRANCH."

# ----- Step 3: Install Composer Dependencies -----
info "Step 3/10: Installing Composer dependencies (production)..."
cd "$PROJECT_ROOT"
"$COMPOSER_PATH" install --no-dev --prefer-dist --no-interaction --optimize-autoloader --no-progress
ok "Composer dependencies installed."

# ----- Step 4: Install Node Dependencies & Build Assets -----
if [ "$SKIP_NPM" = false ]; then
    info "Step 4/10: Installing Node dependencies..."
    if [ -x "$NODE_PATH" ] && [ -x "$NPM_PATH" ]; then
        "$NPM_PATH" install --no-audit --no-fund --production=false
        ok "Node dependencies installed."

        info "Building frontend assets with Vite..."
        "$NPM_PATH" run build
        ok "Frontend assets built."
    else
        warn "Node/NPM not found. Skipping frontend build."
        warn "Pre-built assets must exist in public/build/ or set DEPLOY_NODE_PATH/DEPLOY_NPM_PATH."
    fi
else
    info "Step 4/10: Skipping npm install & build (--skip-npm)."
fi

# Verify build output exists
if [ ! -d "$PROJECT_ROOT/public/build" ]; then
    warn "public/build/ directory not found. Frontend assets may not load correctly."
    warn "Ensure 'npm run build' has been run or remove --skip-npm flag."
fi

# ----- Step 5: Run Database Migrations -----
if [ "$SKIP_MIGRATE" = false ]; then
    info "Step 5/10: Running database migrations..."
    cd "$PROJECT_ROOT"
    "$PHP_PATH" "$PROJECT_ROOT/artisan" migrate --force --no-interaction
    ok "Migrations completed."
else
    info "Step 5/10: Skipping migrations (--skip-migrate)."
fi

# ----- Step 6: Optimize Laravel Caches -----
info "Step 6/10: Optimizing Laravel caches..."
cd "$PROJECT_ROOT"

# Clear all caches first (idempotent)
"$PHP_PATH" "$PROJECT_ROOT/artisan" config:clear
"$PHP_PATH" "$PROJECT_ROOT/artisan" route:clear
"$PHP_PATH" "$PROJECT_ROOT/artisan" view:clear
"$PHP_PATH" "$PROJECT_ROOT/artisan" event:clear
"$PHP_PATH" "$PROJECT_ROOT/artisan" cache:clear

# Then cache for production
"$PHP_PATH" "$PROJECT_ROOT/artisan" config:cache
"$PHP_PATH" "$PROJECT_ROOT/artisan" route:cache
"$PHP_PATH" "$PROJECT_ROOT/artisan" view:cache
"$PHP_PATH" "$PROJECT_ROOT/artisan" event:cache

ok "Laravel caches optimized."

# ----- Step 7: Create Storage Symlink -----
info "Step 7/10: Creating storage symlink..."
cd "$PROJECT_ROOT"

# Remove existing symlink/directory if it exists
if [ -L "$PROJECT_ROOT/public/storage" ]; then
    rm "$PROJECT_ROOT/public/storage"
elif [ -d "$PROJECT_ROOT/public/storage" ]; then
    rm -rf "$PROJECT_ROOT/public/storage"
fi

"$PHP_PATH" "$PROJECT_ROOT/artisan" storage:link
ok "Storage symlink created."

# ----- Step 8: Set File Permissions -----
info "Step 8/10: Setting file permissions..."
cd "$PROJECT_ROOT"

# Find the web server user (cPanel typically uses the account user)
WEB_USER=""
if id "www-data" &>/dev/null; then
    WEB_USER="www-data"
elif id "apache" &>/dev/null; then
    WEB_USER="apache"
elif id "nobody" &>/dev/null; then
    WEB_USER="nobody"
else
    # cPanel: the user running the script is typically the account owner
    # and also the web server user for that account
    WEB_USER="$(whoami)"
    warn "Using current user '$WEB_USER' as web server user."
fi

# Directories that must be writable by the web server
WRITABLE_DIRS=(
    "bootstrap/cache"
    "storage"
    "storage/framework"
    "storage/framework/cache"
    "storage/framework/sessions"
    "storage/framework/views"
    "storage/logs"
    "storage/app"
    "storage/app/public"
)

# Create directories if they don't exist (idempotent)
for dir in "${WRITABLE_DIRS[@]}"; do
    mkdir -p "$PROJECT_ROOT/$dir"
done

# Set ownership (cPanel: user owns everything)
if [ -n "$WEB_USER" ]; then
    # Only chown if we're root or the owner is different
    if [ "$(id -u)" = "0" ]; then
        chown -R "$WEB_USER:$WEB_USER" "$PROJECT_ROOT/storage"
        chown -R "$WEB_USER:$WEB_USER" "$PROJECT_ROOT/bootstrap/cache"
    fi
fi

# Set directory permissions to 755, files to 644 (secure defaults)
find "$PROJECT_ROOT" -type d -not -path "*/node_modules/*" -not -path "*/vendor/*" -not -path "*/.git/*" -exec chmod 755 {} + 2>/dev/null || true
find "$PROJECT_ROOT" -type f -not -path "*/node_modules/*" -not -path "*/vendor/*" -not -path "*/.git/*" -exec chmod 644 {} + 2>/dev/null || true

# Writable directories need 775
for dir in "${WRITABLE_DIRS[@]}"; do
    chmod -R 775 "$PROJECT_ROOT/$dir" 2>/dev/null || true
done

# Artisan must be executable
chmod 755 "$PROJECT_ROOT/artisan"

# Deploy scripts must be executable
chmod 755 "$PROJECT_ROOT/deploy/deploy.sh" 2>/dev/null || true
chmod 755 "$PROJECT_ROOT/deploy/update.sh" 2>/dev/null || true
chmod 755 "$PROJECT_ROOT/deploy/rollback.sh" 2>/dev/null || true

ok "File permissions set."

# ----- Step 9: Queue & Scheduled Tasks Setup -----
info "Step 9/10: Verifying queue and scheduler setup..."

# Start the queue worker if using database driver (supervisor should manage this in production)
QUEUE_DRIVER=$("$PHP_PATH" "$PROJECT_ROOT/artisan" tinker --execute="echo config('queue.default');" 2>/dev/null || echo "sync")
info "Queue driver: $QUEUE_DRIVER"
if [ "$QUEUE_DRIVER" = "database" ]; then
    info "Note: Ensure a supervisor process or cron job runs: php artisan queue:work --daemon"
fi

# Remind about cron setup for scheduler
CRON_EXISTS=$(crontab -l 2>/dev/null | grep -c "artisan schedule:run" || echo "0")
if [ "$CRON_EXISTS" = "0" ]; then
    warn "Laravel scheduler cron not found. Add this to your crontab:"
    warn "* * * * * $PHP_PATH $PROJECT_ROOT/artisan schedule:run >> /dev/null 2>&1"
fi

ok "Queue and scheduler verification done."

# ----- Step 10: Bring Application Back Up -----
info "Step 10/10: Bringing application back up..."
cd "$PROJECT_ROOT"
"$PHP_PATH" "$PROJECT_ROOT/artisan" up

# Save deployment info
DEPLOY_TIMESTAMP=$(date +"%Y-%m-%d %H:%M:%S")
DEPLOY_COMMIT=$(git rev-parse --short HEAD 2>/dev/null || echo "unknown")
echo "$DEPLOY_TIMESTAMP | $BRANCH | $DEPLOY_COMMIT" >> "$PROJECT_ROOT/deploy/last_release.txt"

ok "========================================="
ok "Deployment COMPLETE!"
ok "========================================="
ok "Timestamp : $DEPLOY_TIMESTAMP"
ok "Branch    : $BRANCH"
ok "Commit    : $DEPLOY_COMMIT"
ok "App URL   : $(grep '^APP_URL=' "$PROJECT_ROOT/.env" | cut -d'=' -f2-)"
ok "========================================="
ok "If something is wrong, run: ./deploy/rollback.sh"
