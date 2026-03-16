# Primix Tables

`primix/tables` is an official package in the Primix ecosystem.
It is part of the Primix framework and provides a table builder for advanced data listings in admin panels.

## What it is for

- Build tables with columns, filters, sorting, search, and pagination.
- Handle row actions, bulk actions, and table state consistently.
- Reuse listing logic across resources, pages, and widgets.

## Installation

Recommended for full Primix projects:

```bash
composer require primix/primix
```

Standalone module installation:

```bash
composer require primix/tables
```

## Quick example

```php
use Primix\Tables\Table;
use Primix\Tables\Columns\TextColumn;

public static function table(Table $table): Table
{
    return $table
        ->query(Product::query())
        ->columns([
            TextColumn::make('name')->searchable(),
        ]);
}
```
