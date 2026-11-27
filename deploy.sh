#!/usr/bin/env bash
#
# تحديث المنصة على السيرفر بأمر واحد.
# Update the platform on the server in one command.
#
# Run it from the Laravel core folder (gts_core):
#     cd ~/domains/gto.com.ly/gts_core && bash deploy.sh
#
# What it does:
#   1. Refuses to run if files tracked by git were edited by hand on the server.
#   2. Pulls the latest code from GitHub (master).
#   3. Copies the public files (build/, css/, js/, images/ ...) into public_html, which is what the
#      website actually serves. index.php and .htaccess in public_html are never touched,
#      and nothing is deleted from public_html.
#   4. Runs `composer install` only when composer.lock changed.
#   5. Asks before running database migrations, and only when new migrations arrived.
#   6. Clears Laravel's caches.
#
# Options (environment variables):
#   BRANCH=master            branch to deploy
#   PUBLIC_HTML=../public_html   folder the website serves
#   RUN_MIGRATIONS=yes       run new migrations without asking
#   NO_PULL=1                skip the git pull (used when rolling back to an older commit)

set -euo pipefail

BRANCH="${BRANCH:-master}"
CORE_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PUBLIC_HTML="${PUBLIC_HTML:-$CORE_DIR/../public_html}"

green() { printf '\033[32m%s\033[0m\n' "$*"; }
yellow() { printf '\033[33m%s\033[0m\n' "$*"; }
red() { printf '\033[31m%s\033[0m\n' "$*" >&2; }
step() { printf '\n\033[1;36m==> %s\033[0m\n' "$*"; }

cd "$CORE_DIR"

if [ ! -f artisan ] || [ ! -d .git ]; then
    red "This script must live in the Laravel core folder (gts_core) that is a git checkout."
    exit 1
fi

if [ ! -d "$PUBLIC_HTML" ]; then
    red "public_html folder not found at: $PUBLIC_HTML"
    red "Set it explicitly, e.g.: PUBLIC_HTML=/path/to/public_html bash deploy.sh"
    exit 1
fi
PUBLIC_HTML="$(cd "$PUBLIC_HTML" && pwd)"

# ---------------------------------------------------------------------------
step "1/6 Checking for manual edits on the server"
if ! git diff --quiet || ! git diff --cached --quiet; then
    red "Some files tracked by git were changed directly on the server:"
    git status --short --untracked-files=no >&2
    red "Nothing was changed. Review these edits first (copy them to the repository or discard them with: git checkout -- <file>)."
    exit 1
fi
green "No manual edits."

# ---------------------------------------------------------------------------
step "2/6 Pulling the latest code ($BRANCH)"
OLD_HEAD="$(git rev-parse HEAD)"
if [ "${NO_PULL:-}" = "1" ]; then
    yellow "NO_PULL=1: keeping the current commit $(git log -1 --format='%h %s')"
else
    git fetch origin "$BRANCH"

    if ! git merge-base --is-ancestor HEAD "origin/$BRANCH"; then
        red "The server history and GitHub have diverged (for example after a history rewrite)."
        red "Nothing was changed. If GitHub is the version to keep, run:"
        red "    git branch backup-\$(date +%Y%m%d-%H%M) && git reset --hard origin/$BRANCH && bash deploy.sh"
        exit 1
    fi

    git merge --ff-only "origin/$BRANCH" >/dev/null
fi
NEW_HEAD="$(git rev-parse HEAD)"

if [ "$OLD_HEAD" = "$NEW_HEAD" ]; then
    green "Already up to date ($(git log -1 --format='%h %s'))."
else
    green "Updated: $(git rev-parse --short "$OLD_HEAD") -> $(git rev-parse --short "$NEW_HEAD")"
    git log --format='   %h %s' "$OLD_HEAD..$NEW_HEAD"
fi

changed() { [ "$OLD_HEAD" != "$NEW_HEAD" ] && ! git diff --quiet "$OLD_HEAD" "$NEW_HEAD" -- "$@"; }

# Go back to the previous commit when the new code cannot run (e.g. its libraries cannot be installed)
rollback() {
    if [ "$OLD_HEAD" != "$NEW_HEAD" ]; then
        git reset --hard "$OLD_HEAD" >/dev/null
        red "Rolled back to the previous version ($(git rev-parse --short HEAD)); the site keeps running it."
    fi
}

# Composer: on PATH, or installed in the home folder / project (shared hosting usually has none)
COMPOSER_CMD=""
if command -v composer >/dev/null 2>&1; then
    COMPOSER_CMD="composer"
else
    for candidate in "$HOME/bin/composer" "$HOME/composer.phar" "$HOME/bin/composer.phar" "$CORE_DIR/composer.phar"; do
        if [ -f "$candidate" ]; then COMPOSER_CMD="php $candidate"; break; fi
    done
fi

# PHP libraries must match composer.lock: after a change to it, or when vendor/ is older than it
NEED_COMPOSER=0
if changed composer.lock; then
    NEED_COMPOSER=1
elif [ -n "$COMPOSER_CMD" ] && { [ ! -f vendor/composer/installed.json ] || [ composer.lock -nt vendor/composer/installed.json ]; }; then
    # vendor/ may be stale even when this pull did not touch composer.lock (e.g. code pulled by hand)
    NEED_COMPOSER=1
fi
if [ "$NEED_COMPOSER" = "1" ] && [ -z "$COMPOSER_CMD" ]; then
    red "This update changes the PHP libraries (composer.lock) but Composer is not installed on this server."
    rollback
    red "Install Composer once (see the instructions in this project's README or ask the developer), then run: bash deploy.sh"
    exit 1
fi

# ---------------------------------------------------------------------------
step "3/6 Copying public files to $PUBLIC_HTML"
# Everything under public/ except the entry point and server config that public_html keeps its own copy of.
EXCLUDES=(index.php .htaccess hot storage)
if command -v rsync >/dev/null 2>&1; then
    RSYNC_EXCLUDES=()
    for item in "${EXCLUDES[@]}"; do RSYNC_EXCLUDES+=(--exclude "/$item"); done
    rsync -a "${RSYNC_EXCLUDES[@]}" "$CORE_DIR/public/" "$PUBLIC_HTML/"
else
    TAR_EXCLUDES=()
    for item in "${EXCLUDES[@]}"; do TAR_EXCLUDES+=(--exclude "./$item"); done
    (cd "$CORE_DIR/public" && tar -cf - "${TAR_EXCLUDES[@]}" .) | (cd "$PUBLIC_HTML" && tar -xf -)
fi

if [ ! -f "$PUBLIC_HTML/build/manifest.json" ]; then
    red "build/manifest.json is missing in public_html; the site's CSS/JS will not load."
    exit 1
fi
green "Public files copied (index.php and .htaccess in public_html left as they are)."

# ---------------------------------------------------------------------------
step "4/6 PHP dependencies"
if [ "$NEED_COMPOSER" = "1" ]; then
    if ! $COMPOSER_CMD install --no-dev --optimize-autoloader --no-interaction; then
        red "composer install failed."
        rollback
        # Put the libraries of the previous version back
        $COMPOSER_CMD install --no-dev --optimize-autoloader --no-interaction >/dev/null 2>&1 || true
        php artisan optimize:clear >/dev/null 2>&1 || true
        exit 1
    fi
else
    green "PHP libraries already match composer.lock, skipping."
fi

# ---------------------------------------------------------------------------
step "5/6 Database migrations"
if changed database/migrations; then
    yellow "New or changed migrations:"
    git diff --name-only "$OLD_HEAD" "$NEW_HEAD" -- database/migrations | sed 's/^/   /'
    answer="${RUN_MIGRATIONS:-}"
    if [ -z "$answer" ] && [ -t 0 ]; then
        read -r -p "Run them now? Back up the database first. [y/N] " answer
    fi
    case "$answer" in
        y|Y|yes|YES) php artisan migrate --force ;;
        *) yellow "Skipped. Run 'php artisan migrate --force' yourself when ready." ;;
    esac
else
    green "No new migrations."
fi

# ---------------------------------------------------------------------------
step "6/6 Clearing caches"
php artisan optimize:clear

printf '\n'
green "Deploy finished: $(git log -1 --format='%h %s')"
if [ "$OLD_HEAD" != "$NEW_HEAD" ]; then
    printf 'To go back to the previous version:\n    git reset --hard %s && NO_PULL=1 bash deploy.sh\n' "$(git rev-parse --short "$OLD_HEAD")"
fi
