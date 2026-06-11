# Yugumo Note 2024 Development Guide

## Project Overview

- **Name**: Yugumo - Note taking web application (namesake: [Japanese destroyer Yugumo (1941)](https://en.wikipedia.org/wiki/Japanese_destroyer_Y%C5%ABgumo_(1941)))
- **Stack**: TALL — Tailwind 4.3, Alpine.js 3, Laravel 11, Livewire 3
- **Runtime**: PHP 8.3, Composer 2, Node.js 26, PostgreSQL 15.1
- **Timezone**: UTC
- **Debugging**: [Laravel Telescope](https://laravel.com/docs/11.x/telescope) installed

## Development Environment

### Docker Access

```bash
docker compose exec yugumo-note-2024-localhost bash
```

### Local URL Endpoints

- **Application**: http://yugumo-note-2024.localhost
- **Telescope**: http://yugumo-note-2024.localhost/telescope

## Code Quality (REQUIRED after code changes)

Run inside the PHP container:

```bash
./vendor/bin/pint
```

### Code Conventions

- **PHP Format**: Laravel (via pint)
- **Naming**:
    - Variables/functions: camelCase
    - Classes/files: PascalCase
- **String quotes**: Prefer single quotes `' '` over double quotes `" "`
- **Typing**: Use strong typing as much as possible
- **Routes**: Use `-` as separator (e.g., `/user/get-user-info`)
- **Frontend**: Prefer Livewire/Volt components for interactivity; Alpine.js for small client-side behaviors
- **Model section order**: Using traits → Table structure → Configuration (casts, guarded, fillable, hidden) → Mutators & Accessors → Relationships

### Architecture

- **Domain structure**: `app/Features/{FeatureName}/` (one folder per feature)
- **Current features**: `Note`, `NoteType`, `Search`, `User`
- **Feature sub-directories** (create only what is needed):
    - `Actions/` — reusable business/application logic
    - `Authorizers/` — authorization logic
    - `ArtisanCommands/` — custom artisan commands
    - `Validators/` — validation logic
    - `Jobs/` — queue jobs
    - `Notifications/` — notifications
    - `ValueObjects/` — structured data containers
    - `Cache/` — caching related
    - `Middlewares/` — feature middlewares
    - `Enums/` — feature scoped enums
    - `Commands/` — reusable write-to-database logic
    - `Queries/` — reusable read-from-database logic
        - `Filters/` — applied via `filter` query string
        - `Sorts/` — applied via `sort` query string
    - `Models/`
        - `Relationships/` — reusable relationship interfaces
        - `*.php` — Eloquent models; contain only mutators, accessors, and no business logic
- **Extendables**: `app/Extendables/{Core,Providers}/` — base classes, interfaces, traits, and service providers
- **HTTP layer**: `app/Http/{Authentication,Note,Profile}/` — Livewire/Volt components and HTTP entry points
- **Global enums**: `app/Enums/`
- **Query strings**: Follow [JSON API specification](https://jsonapi.org/format/#fetching) (`filter`, `sort`, `include`, `page`, `only`)

### Macros

Macros are registered in `app/Extendables/Providers/ExtendableServiceProvider.php`. **Do not re-implement these — reuse them.**

#### Query builder macros

- `whereEmpty($field)` — matches `NULL` or empty string
- `whereNotEmpty($field)` — matches non-null and non-empty

#### Str macros

- `replaceSlash($str, $replace = '-')` — replaces `/` and `\` with `$replace`
- `hashSha256($str)` — SHA-256 hash of the string

## Security & Secret Handling

- Never commit `.env` files or any file containing secrets
- All secrets must be stored in environment variables, not in code
- Sensitive `.env` fields:
    - `APP_KEY`
    - `DB_PASSWORD`
    - `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`
    - `MAIL_PASSWORD`
    - `REDIS_PASSWORD` (if used in future)
- Files to never commit: `.env`, `*.pem`, `*.key`
- Use `.env.example` as a template with placeholder values only
- Never log secrets or sensitive data
- Never hardcode credentials in source code
- Use Laravel's `env()` function only in config files; use `config()` values in application code

## Database Note: PostgreSQL + PGroonga

The DB image is `groonga/pgroonga:4.0.1-alpine-15` (see `docker-compose.yml`). It provides the [PGroonga](https://pgroonga.github.io/) extension for high-performance full-text search, which is used by `app/Features/Search/`.

- **Do not** replace the image with a plain `postgres` image without confirming the search feature can be re-implemented.
- **Do not** write MySQL/MariaDB-specific DDL or queries.

## Git Conventions

### Branch Naming

Template: `{group}/#{issue-id}-{name}`

| Group  | Description         |
|:-------|:--------------------|
| feat   | Feature code        |
| fix    | Bug fix code        |
| hotfix | Hotfix code         |
| ref    | Refactor code       |
| test   | Unit test code      |
| conf   | Configuration code  |
| chore  | Other kinds of code |

Examples:

- `feat/#3-authentication`
- `conf/update-access-token-lifetime`

### Commit Message

Template:

```
[type]: [Short summary in sentence case]

- [Bullet point 1: Describe the main change, referencing files or modules if relevant.]
- [Bullet point 2: List any additional changes, improvements, or fixes.]
- [Bullet point 3: Mention any documentation updates, dependency changes, or clarifications.]
```

Commit types:

- feat: feature code
- fix: bug fix code
- hotfix: hotfix code
- ref: refactor code
- test: unit test code
- conf: configuration code
- chore: other kinds of code
- doc: documentation

Guidelines:

- Use the correct commit type
- Keep the summary concise and in sentence case
- Use clear, specific bullet points for each change
- Reference files, modules, or dependencies when relevant
- Avoid overly verbose descriptions or unnecessary details
- Do not include unrelated changes in the same commit message

Example:

```
chore: Update queue config to use Redis and revise README

- Set the default queue connection to `redis` in `queue.php`.
- Update system requirements and dependencies in README for Laravel 12, PHP 8.3, Postgres 17, and Redis 7.
- Fix typos and improve phrasing for clarity.
```

## Key Artisan Commands

```bash
php artisan key:generate             # Generate APP_KEY
php artisan migrate:fresh --seed     # Fresh DB with seed data
php artisan db:seed                  # Run seeders only
php artisan optimize                 # Cache config/routes/views
php artisan optimize:clear           # Clear cached config/routes/views
php artisan telescope:clear          # Clear Telescope entries
php artisan about                    # Show application info
```

## Fly.io Deployment

- Region: `sin` (Singapore)
- Install [flyctl](https://fly.io/docs/flyctl/) before deploying
- All setup is in the source code (`Dockerfile`, `fly.toml`)
- Release command runs `php artisan migrate --force` automatically
- Deploy:

```bash
fly deploy
```
