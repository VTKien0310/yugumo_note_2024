## Project specification

- TALL stack:
    - Tailwind 4
    - Alpine.js 3
    - Laravel 11
    - Livewire 3
- Composer 2
- Timezone: UTC

## System requirement

- PHP 8.3
- Postgres 15.1

## Dependencies

- [Laravel Telescope for debugging](https://laravel.com/docs/11.x/telescope)

## Structure

### Inspiration

- [Laravel beyond CRUD: Domain oriented Laravel](https://online.fliphtml5.com/pbudi/dfap/#p=6)
- [Laravel beyond CRUD: Working with data](https://online.fliphtml5.com/pbudi/dfap/#p=6)
- [Laravel beyond CRUD: Actions](https://online.fliphtml5.com/pbudi/dfap/#p=6)
- [Laravel beyond CRUD: Models](https://online.fliphtml5.com/pbudi/dfap/#p=6)
- [Effective Eloquent queries](https://laravel-news.com/effective-eloquent)
- [JSON API specification's query string format](https://jsonapi.org/format/#fetching)

### Detail

app/

- Enums/: application's global enums
- Extendables/: base classes, interfaces, and traits for reuse across the entire application. It has its own README.md
  in the directory
- Features/: features related code
    - Actions/: reusable business and application logic
    - Authorizers/: authorization logic
    - ArtisanCommands/: custom artisan commands
    - Validators/: validation logic
    - Jobs/: queue jobs
    - Notifications/: notifications
    - ValueObjects/: classes used to structure data instead of using unstructured and hard to predict arrays
    - Cache/: caching related
    - Middlewares/: features related middlewares
    - Enums/: feature related enums
    - Commands/: reusable write to database logic
    - Queries/: reusable read from database logic
        - Filters/: filters to be applied based on request query string for index queries
        - Sorts/: sorts to be applied based on request query string for index queries
    - Models/:
        - Relationships/: relationship interfaces for better typing and reuse of repetitive relationships
        - .php: represents a record in the data source. Models should ony contain mutators, accessors and no business
          logic
- Http/: HTTP layer code
    - Controller.php: controller for api endpoints
    - routes.php: api routing definition
- Ports/: external or third party services interaction

## Setup

### Install dependencies

#### For local development environment:

```
composer install
```

#### For production environment:

```
composer install --no-dev
```

### Config .env

```
cp .env.example .env
```

Important fields:

- APP_*
- DB_*
- SESSION_*

### Initialize

```
php artisan key:generate
php artisan migrate:fresh
php artisan db:seed
php artisan optimize
```

## Fly.io deployment

- More details can be found on the Fly.io official [document](https://fly.io/docs/laravel/).
- All setup has been done in the source code.
- Install [flyctl](https://fly.io/docs/flyctl/) before proceeding with the deployment.
- To deploy the application, run the following command:

```shell
fly deploy
```

## Code style fixer

```shell
./vendor/bin/pint
```

## Macros

Macros are registered in `ExtendableServiceProvider.php`

### Query builder macros

- whereEmpty
- whereNotEmpty

### Str macros

- replaceSlash
- hashSha256
- hashEachByteSha256

## Conventions and standards

### Model

A Model class file should be organized into sections with the following order:

- Using traits section.
- ***Table structure*** section defining the Model's table and the Model's attributes as constants. This is intended for
  more convenient typing in IDE and easier maintenance and updating of table's columns.
- ***Configuration*** section defining the Model's casts, guarded, fillable, and hidden attributes.
- ***Mutators & Accessors*** section.
- ***Relationship*** section.
