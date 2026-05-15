# Laravel AirTable Manager

Interact with Airtable using a fluent API.

Query, insert, update and manage Airtable records using an expressive Laravel-style syntax.

## Installation

Require this package with composer.
```shell
composer require straylightagency/laravel-airtable
```

Define your environment variables into your .env file :
```dotenv
AIRTABLE_BASE_ID="app**************"
AIRTABLE_API_KEY="key**************"
AIRTABLE_API_URL="https://api.airtable.com/v0/%s/" # optional
```

Use this artisan command to publish the airtable.php file inside your config folder :
```shell
php artisan vendor:publish --tag=airtable
```

### Laravel without auto-discovery:

If you don't use auto-discovery, add the `AirTableServiceProvider` to the providers array in `bootstrap/providers.php`:
```php
\Straylightagency\LaravelAirTable\AirTableServiceProvider::class,
```

Then add this line to your facades in `config/app.php`:
```php
'AirTable' => \Straylightagency\LaravelAirTable\AirTable::class,
```

## Usage

The package provides by default a Facade for Laravel application. You can call methods directly using the Facade or use the alias instead.

```php
use Straylightagency\LaravelAirTable\AirTable;

$recordsA = AirTable::table('Your table')->view('View')->get();
$recordsB = AirTable::table('Another table')->where('key', '=', 'value' )->view('In view this view')->get();
```

### API documentation

#### AirTableManager
```php
/**
 * Get a builder for a table from the default Base set in constructor
 */
function table(string $table_name): Table;

/**
 * Create a Base builder object with a new Client, using the same API key and API url set in your config.
 */
function on(string $base_id): Base;
```

#### Base
```php
/**
 * Get a table builder for a table.
 */
function table(string $table_name): Table;
```

#### Table
```php
/**
 * Count the number of elements inside the query
 */
function count(): int;

/**
 * If AirTable must perform an automatic data conversion from string values
 */
function typecast(bool $value): Table;

/**
 * Delay between request
 */
function delay(int $value): Table;

/**
 * Search for specific fields from records
 */
function fields(array|string $fields): Table;

/**
 * Filter records using a logical where operation
 */
function where(string $field, mixed $operator, $value = null): Table;

/**
 * Filter records using a raw query
 */
function whereRaw(string $formula): Table;

/**
 * Get records from a specific view
 */
function view(string $view_name): Table;

/**
 * Order records by a field and direction
 */
function orderBy(string $field, string $direction = 'asc'): Table;

/**
 * Set the limit value to get a limited number of records
 */
function limit(int $value): Table;

/**
 * Alias to limit method
 */
function take(int $value): Table;

/**
 * Set the offset value to get records from a specific page
 */
function offset(int $value): Table;

/**
 * Alias to offset method
 */
function skip(int $value): Table;

/**
 * Get records with a limit of 100 by page
 */
function get(): array;

/**
 * Method alias to get, return all records
 */
function all(): array;

/**
 * Get the first record
 */
function first(): array;

/**
 * Find a record using his ID
 */
function find(string $id): array;

/**
 * Insert a record
 */
function insert(array $data): array;

/**
 * Update a record or many records. Destructive way
 */
function update(array|string $id, array $data = null): array;

/**
 * Patch a single record or many records
 */
function patch(array|string $id, array $data = null): array;

/**
 * Delete a single record
 */
function delete(string $id): array;
```

### Requirement

PHP 8.3 or above