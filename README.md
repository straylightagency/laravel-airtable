# Laravel Airtable

A fluent Airtable client for Laravel applications.

Query, insert, update and manage Airtable records using an expressive Laravel-style API.

---

## Features

- Fluent Laravel-style query builder
- Airtable CRUD operations
- Multi-base support
- Laravel Facade support
- Lightweight and dependency-free
- PHP 8.3+ compatible

---

## Installation

Install the package using Composer:

```bash
composer require straylightagency/laravel-airtable
```

---

## Laravel Integration

### Auto-discovery

The package supports Laravel auto-discovery out of the box.

### Manual registration

If auto-discovery is disabled, register the service provider manually in `bootstrap/providers.php`:

```php
Straylightagency\LaravelAirtable\AirtableServiceProvider::class,
```

### Configuration

Add your Airtable credentials to your `.env` file:

```dotenv
AIRTABLE_BASE_ID="appXXXXXXXXXXXXXX"
AIRTABLE_TOKEN="patXXXXXXXXXXXXXX"
AIRTABLE_API_URL="https://api.airtable.com/v0/%s/" # optional
```

Use this artisan command to publish the `airtable.php` file inside your config folder :

```shell
php artisan vendor:publish --tag=airtable
```

---

## Quick Start

```php
use Straylightagency\LaravelAirtable\Facades\Airtable;

$records = Airtable::table('Users')
    ->view('Active users')
    ->get();
```

---

## Usage

### Selecting records

```php
$records = Airtable::table('Users')->get();
```

### Filtering records

```php
$records = Airtable::table('Users')
    ->where('status', '=', 'active')
    ->get();
```

### Multiple conditions

```php
$records = Airtable::table('Users')
    ->where('status', 'active')
    ->where('country', 'Belgium')
    ->get();
```

### Using views

```php
$records = Airtable::table('Users')
    ->view('Public')
    ->get();
```

### Selecting specific fields

```php
$records = Airtable::table('Users')
    ->fields(['name', 'email'])
    ->get();
```

### Ordering records

```php
$records = Airtable::table('Users')
    ->orderBy('name')
    ->get();
```

### Limiting results

```php
$records = Airtable::table('Users')
    ->limit(10)
    ->get();
```

### Pagination

```php
$records = Airtable::table('Users')
    ->limit(50)
    ->offset(100)
    ->get();
```

---

## CRUD Operations

### Find a record

```php
$record = Airtable::table('Users')
    ->find('recXXXXXXXXXXXXXX');
```

### Get first record

```php
$record = Airtable::table('Users')
    ->where('email', 'john@example.com')
    ->first();
```

### Insert a record

```php
$record = Airtable::table('Users')->insert([
    'Name' => 'John Doe',
    'Email' => 'john@example.com',
]);
```

### Update a record

```php
$record = Airtable::table('Users')->update(
    'recXXXXXXXXXXXXXX',
    [
        'Name' => 'John Doe',
    ]
);
```

### Patch a record

```php
$record = Airtable::table('Users')->patch(
    'recXXXXXXXXXXXXXX',
    [
        'Name' => 'Updated name',
    ]
);
```

### Delete a record

```php
$record = Airtable::table('Users')
    ->delete('recXXXXXXXXXXXXXX');
```

---

## Multi-base Support

You can dynamically switch Airtable bases:

```php
$records = Airtable::on('appXXXXXXXXXXXXXX')
    ->table('Users')
    ->get();
```

---

## API Reference

### AirtableManager

```php
table(string $table_name): Table
on(string $base_id): Base
```

### Base

```php
table(string $table_name): Table
```

### Table

```php
count(): int

typecast(bool $value): Table

delay(int $value): Table

fields(array|string $fields): Table

where(string $field, mixed $operator, $value = null): Table

whereRaw(string $formula): Table

view(string $view_name): Table

orderBy(string $field, string $direction = 'asc'): Table

limit(int $value): Table

take(int $value): Table

offset(int $value): Table

skip(int $value): Table

get(): array

all(): array

first(): array

find(string $id): array

insert(array $data): array

update(array|string $id, array $data = null): array

patch(array|string $id, array $data = null): array

delete(string $id): array
```

---

## Requirements

- PHP 8.3+
- Laravel 11+

---

## License

This package is open-sourced software licensed under the MIT license.