# Forum — Laravel · Inertia.js · Vue 3

A forum built with **Laravel 13**, **Inertia.js v3** and **Vue 3 (TypeScript)**. It is a refactoring of my university forum, which was originally built with vanilla PHP and vanilla JS.

## Tech Stack

| Layer | Technology |
| --- | --- |
| Backend | Laravel 13 (PHP 8.5), Eloquent, Sanctum API auth |
| Frontend framework | Inertia.js v3 + Vue 3 + TypeScript |
| Bundler | Vite (`@inertiajs/vite` plugin) |
| Styling | Bootstrap 5 + Bootstrap Icons |
| Real-time messaging | Laravel Reverb (WebSockets) + Laravel Echo |
| Image uploads | spatie/laravel-medialibrary + FilePond (avatar & banner) |
| Notifications | SweetAlert2 (`vue-sweetalert2`) modal toasts |
| URL generation in Vue | Ziggy |
| Testing | Pest (+ Playwright browser plugin) |

## Features

- **Sign up / login / logout** with session regeneration and CSRF protection
- **Posts** — create, edit and delete (author only), organised into categories
- **Comments** — create, edit and delete on any post (paginated, loaded lazily via Inertia deferred props)
- **Categories page** to browse topics by category; the home feed can be filtered by category
- **Public user profiles** (`/user/profile/{username}`) showing avatar, banner and that user's posts
- **Profile management** — update name, username, email and password, upload a profile picture and banner image (FilePond + Media Library), or delete your account entirely
- **Real-time private messaging** between users over Reverb WebSockets (`chat.{id}` private channels)
- **Inertia v3 conveniences** — deferred props with skeleton loading states, `once` props for data that never changes, and server-side pagination
- Standard Laravel protections throughout: FormRequest validation, authorisation checks (owner-only edits/deletes), CSRF tokens

## Pages & Screenshots

### Home / Topics feed

Paginated list of the latest posts. Can be filtered by category via query string.

![Home](docs/screenshots/home.png)

### Categories

Browse all forum categories and their topics.

![Categories](docs/screenshots/categories.png)

### Post view

A single post with its author, plus a paginated comments section that loads lazily (deferred prop).

![Post](docs/screenshots/post.png)

### Create / edit post

The upsert form used for both creating new posts and editing your own.

![Post form](docs/screenshots/post-form.png)

### Public profile

A user's public page: avatar, banner, details and their posts (deferred).

![Profile](docs/screenshots/profile.png)

### Edit profile

Update account details and upload a new profile picture or banner image via FilePond.

![Edit profile](docs/screenshots/profile-edit.png)

### Register

Guest registration form (also supports uploading an avatar on sign up).

![Register](docs/screenshots/register.png)

The **login** page and the real-time **chats / messages** pages follow the same design language but have no committed screenshots.

## How to Run the Project

1. Copy `.env.example` to `.env`, run `php artisan key:generate`, and configure your database credentials and `APP_URL` (important — uploaded images are served through full URLs).
2. Install PHP dependencies: `composer install`
3. Install JS dependencies (the project uses pnpm): `pnpm install`
4. Run migrations and seeders: `php artisan migrate --seed`
5. Link the storage directory so uploaded media is publicly accessible: `php artisan storage:link`
6. Build or serve assets: `pnpm run dev` (development) or `pnpm run build` (production)
7. Serve the app: `php artisan serve`

### Real-time messaging setup

The chat feature broadcasts through Laravel Reverb. To enable live message delivery, create a Reverb application and set its credentials in `.env`:

```dotenv
BROADCAST_CONNECTION=reverb
REVERB_APP_KEY=...
REVERB_APP_SECRET=...
REVERB_APP_ID=...
REVERB_HOST=...
REVERB_PORT=443
REVERB_SCHEME=https
```

With the default `log` broadcast driver, messages are still stored and displayed on reload — they just aren't pushed in real time.

### Deploying with Kamal 2

Production deploys use [Kamal 2](https://kamal-deploy.org/) with the self-contained `production` stage in `docker/php/Dockerfile` (Nginx + PHP-FPM under Supervisord on port 80, healthcheck at `/up`, plus `worker`, `scheduler` and `reverb` roles). On container startup in production (`APP_ENV=production`), `docker/php/entrypoint.sh` automatically runs `php artisan migrate --force` before starting Supervisord or the role command, so schema migrations succeed before web traffic or queue workers start.

1. Copy `.kamal/secrets.example` to `.kamal/secrets` (gitignored and excluded by `.dockerignore`) and configure your registry token, `APP_KEY`, `APP_URL`, database credentials and Reverb / S3 settings.
2. Set the target host (`export KAMAL_DEPLOY_HOST=<server-ip>`) or update `config/deploy.yml`.
3. Bootstrap accessories and deploy on first run: `kamal setup` (starts PostgreSQL 18 and RustFS accessories, runs database migrations via the production entrypoint, and starts `web`, `worker`, `scheduler` and `reverb`).
4. (Optional) Seed initial forum categories on a fresh database: `kamal app exec -p "php artisan db:seed --force"`.
5. Deploy subsequent releases: `kamal deploy`

### MySQL to PostgreSQL 18 Cutover Runbook

Switching from the previous MySQL + Valkey stack to PostgreSQL 18 starts a new `forum-postgres` volume; Laravel migrations create the schema (including `cache`, `sessions` and `jobs`) but do not copy existing rows. For an installation with existing MySQL data, follow this cutover procedure:

1. **Write cutoff & queue drain:**
   - Put the running MySQL application into maintenance mode (`php artisan down`) to establish the write cutoff and recovery point.
   - Let the Valkey/Redis queue worker drain in-flight jobs until `redis-cli llen queues:default` is `0`, then stop the old `worker` and `scheduler` containers.
2. **Backup MySQL (recovery point):**
   - Take a full logical dump of the MySQL database before changing anything:
     `mysqldump --single-transaction --routines --triggers -u laravel -p laravel > forum-mysql-pre-cutover.sql`
   - Record source row counts across all application tables (`users`, `categories`, `posts`, `comments`, `chats`, `messages`, `media`, `temporary_files`, `replies`, `personal_access_tokens`).
3. **Bootstrap PostgreSQL 18 & import data:**
   - Start the PostgreSQL 18 accessory (`kamal accessory boot postgres`) and allow one production container boot (or `php artisan migrate --force`) to create the PostgreSQL schema.
   - Copy application data from MySQL into the existing PostgreSQL schema (for example with `pgloader --with "data only" --with "reset sequences" mysql://laravel:***@127.0.0.1:3306/laravel pgsql://laravel:***@127.0.0.1:5432/laravel`, or CSV export + `\\copy` in foreign-key order: `users`, `categories`, `posts`, `comments`, `chats`, `messages`, `media`, `temporary_files`, `replies`, `personal_access_tokens`).
4. **Verify row counts & primary-key sequences:**
   - Confirm exact row-count parity between MySQL and PostgreSQL for every table:
     `SELECT table_name, (xpath('/row/cnt/text()', query_to_xml(format('SELECT count(*) AS cnt FROM %I', table_name), false, true, '')))[1]::text::bigint AS row_count FROM information_schema.tables WHERE table_schema = 'public' ORDER BY table_name;`
   - Reset and verify every `BIGSERIAL` sequence so the next inserted ID exceeds `MAX(id)` and cannot collide:
     `SELECT setval(pg_get_serial_sequence(t, 'id'), COALESCE((SELECT MAX(id) FROM public.users), 1), true) FROM (VALUES ('public.users'), ('public.categories'), ('public.posts'), ('public.comments'), ('public.chats'), ('public.messages'), ('public.media'), ('public.temporary_files'), ('public.replies'), ('public.personal_access_tokens'), ('public.failed_jobs'), ('public.jobs')) AS v(t);` (run `setval(pg_get_serial_sequence(t, 'id'), GREATEST(COALESCE(MAX(id), 0), 1), MAX(id) IS NOT NULL)` per table and verify `pg_sequences.last_value >= MAX(id)`).
5. **Switch traffic:**
   - Deploy the PostgreSQL release (`kamal deploy`), verify `GET /up` returns `200`, sign in, and confirm a queued job is consumed from the `jobs` table.
6. **Rollback limits:**
   - Keep the old MySQL volume and `forum-mysql-pre-cutover.sql` untouched until the cutover is signed off.
   - Before traffic is re-enabled on PostgreSQL, rollback is zero-loss by redeploying the previous image against the frozen MySQL volume.
   - **Once live write traffic hits PostgreSQL, rolling back the application container alone will not recover posts, comments, messages, or accounts written to PostgreSQL after the cutoff**; reverting after live traffic requires either accepting the pre-cutover MySQL snapshot as the recovery point (losing post-cutover writes) or exporting new PostgreSQL rows back to MySQL during another maintenance window.
