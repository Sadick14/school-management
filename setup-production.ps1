# Production setup script for the School Management System.
#
# Run this from the project root AFTER `git clone`, on a machine that has
# PHP (7.2+) and Composer available (e.g. via Laravel Herd).
# Built CSS/JS assets are committed to the repo, so Node.js/npm is NOT
# required for a normal setup.
#
#   cd school-management
#   .\setup-production.ps1
#
# See PRODUCTION_SETUP.md for the full walkthrough and database options.

$ErrorActionPreference = "Stop"

Write-Host "== School Management - Production Setup ==" -ForegroundColor Cyan

# 1. .env file
if (-not (Test-Path ".env")) {
    Copy-Item ".env.production.example" ".env"
    Write-Host "Created .env from .env.production.example" -ForegroundColor Green
} else {
    Write-Host ".env already exists, leaving it as-is" -ForegroundColor Yellow
}

# 2. PHP dependencies
Write-Host "`nInstalling PHP dependencies (composer install --no-dev)..." -ForegroundColor Cyan
composer install --no-dev --optimize-autoloader

# 3. App key
$envContent = Get-Content ".env" -Raw
if ($envContent -notmatch "APP_KEY=base64:") {
    Write-Host "`nGenerating application key..." -ForegroundColor Cyan
    php artisan key:generate --force
}

# 5. SQLite database file
$dbPath = "database\database.sqlite"
if (-not (Test-Path $dbPath)) {
    New-Item -ItemType File -Path $dbPath | Out-Null
    Write-Host "`nCreated empty SQLite database at $dbPath" -ForegroundColor Green
} else {
    Write-Host "`nUsing existing database at $dbPath" -ForegroundColor Yellow
}

# 6. Migrations
Write-Host "`nRunning database migrations..." -ForegroundColor Cyan
php artisan migrate --force

# 7. Storage link (for uploaded files / logos)
php artisan storage:link

# 8. Cache config, routes and views for production performance
Write-Host "`nCaching configuration..." -ForegroundColor Cyan
php artisan config:cache
php artisan route:cache
php artisan view:cache

Write-Host "`n== Setup complete ==" -ForegroundColor Green
Write-Host "If this is a brand-new database, run 'php artisan db:seed' to create"
Write-Host "the default roles and admin login, or copy an existing"
Write-Host "database/database.sqlite from your dev machine to keep current data."
Write-Host "See PRODUCTION_SETUP.md for details."
