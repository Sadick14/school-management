# Production Setup (Institution Laptop)

These steps get the School Management System running on a fresh
Windows laptop, cloned from this GitHub repository.

## 1. Prerequisites

Install on the laptop first:

- **[Laravel Herd](https://herd.laravel.com/)** (free) - provides PHP, runs the
  site, and gives it a local URL (e.g. `http://school-management.test`).
  Alternatively any stack with PHP 7.2+, Composer and Node.js/npm.
- **[Git](https://git-scm.com/download/win)**
- **[Node.js](https://nodejs.org/)** (LTS) - includes npm

## 2. Clone the repository

```powershell
git clone https://github.com/Sadick14/school-management.git
cd school-management
```

If using Herd, place the cloned folder inside Herd's sites directory (or add
the folder as a Herd site via the Herd UI) so it gets a `.test` domain.

## 3. Run the setup script

```powershell
.\setup-production.ps1
```

This will:

1. Create `.env` from `.env.production.example` (production mode,
   debug off, SQLite database).
2. Install PHP dependencies (`composer install --no-dev`).
3. Generate the application key.
4. Install JS dependencies and build production assets.
5. Create an empty SQLite database file (if none exists) and run migrations.
6. Create the `public/storage` symlink (for uploaded logos/files).
7. Cache config, routes and views for performance.

## 3b. Or run the steps manually

If you'd rather not run the script, here are the same steps as individual
commands:

```powershell
# Create .env
Copy-Item ".env.production.example" ".env"

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Generate app key (skip if .env already has APP_KEY=base64:...)
php artisan key:generate --force

# Install JS dependencies and build assets
npm install
npm run backend-prod
npm run frontend-prod

# Create an empty SQLite database (skip if copying one over, see step 4)
New-Item -ItemType File -Path "database\database.sqlite"

# Run migrations
php artisan migrate --force

# Storage symlink (for uploaded logos/files)
php artisan storage:link

# Cache config, routes and views for performance
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 4. Set up the database

The SQLite database file (`database/database.sqlite`) is **not** included in
the git repository. Choose one option:

- **Bring existing data (recommended)**: copy `database/database.sqlite`
  from this dev machine onto the laptop, into the `database/` folder,
  *before* running the setup script (or re-run `php artisan migrate` after
  copying it over an empty file created by the script).
- **Start fresh with demo data**: after the script finishes, run:

  ```powershell
  php artisan db:seed
  ```

  This creates default roles/permissions and these login accounts:

  | Role        | Username     | Password |
  |-------------|--------------|----------|
  | Super Admin | `superadmin` | `super99` |
  | Admin       | `admin`      | `demo123` |

  **Change these passwords immediately** after first login.

## 5. Open the site

Visit the site URL (e.g. `http://school-management.test`) in a browser and
log in.

## 6. Updating later

To pull future updates from GitHub and re-apply them:

```powershell
git pull
composer install --no-dev --optimize-autoloader
npm install
npm run backend-prod
npm run frontend-prod
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Notes

- `APP_DEBUG=false` is set, so errors show friendly pages instead of stack
  traces. Details are still logged to `storage/logs/laravel-*.log`.
- After editing `.env`, run `php artisan config:clear` (or `config:cache`
  again) - cached config otherwise ignores `.env` changes.
- `DEVELOPER_MODE_ENABLED=false` keeps internal developer-only routes locked.
