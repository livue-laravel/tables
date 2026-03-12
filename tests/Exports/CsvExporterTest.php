<?php

use Illuminate\Support\Collection;
use Primix\Tables\Exports\CsvExporter;
use Primix\Tables\Exports\ExportColumn;

it('generates CSV with headers from collection', function () {
    $source = collect([
        (object) ['name' => 'Alice', 'email' => 'alice@example.com'],
        (object) ['name' => 'Bob', 'email' => 'bob@example.com'],
    ]);

    $exporter = new CsvExporter();
    $csv = $exporter
        ->source($source)
        ->columns([
            ExportColumn::make('name'),
            ExportColumn::make('email'),
        ])
        ->generate();

    $lines = array_filter(explode("\n", trim($csv)));

    expect($lines)->toHaveCount(3);
    expect($lines[0])->toContain('Name');
    expect($lines[0])->toContain('Email');
    expect($lines[1])->toContain('Alice');
    expect($lines[1])->toContain('alice@example.com');
    expect($lines[2])->toContain('Bob');
    expect($lines[2])->toContain('bob@example.com');
});

it('generates CSV without headers', function () {
    $source = collect([
        (object) ['name' => 'Alice'],
    ]);

    $exporter = new CsvExporter();
    $csv = $exporter
        ->source($source)
        ->columns([
            ExportColumn::make('name'),
        ])
        ->withoutHeader()
        ->generate();

    $lines = array_filter(explode("\n", trim($csv)));

    expect($lines)->toHaveCount(1);
    expect($lines[0])->toContain('Alice');
    expect($lines[0])->not->toContain('Name');
});

it('uses custom delimiter', function () {
    $source = collect([
        (object) ['name' => 'Alice', 'email' => 'alice@example.com'],
    ]);

    $exporter = new CsvExporter();
    $csv = $exporter
        ->source($source)
        ->columns([
            ExportColumn::make('name'),
            ExportColumn::make('email'),
        ])
        ->delimiter(';')
        ->generate();

    $lines = array_filter(explode("\n", trim($csv)));

    expect($lines[0])->toContain(';');
    expect($lines[1])->toContain(';');
});

it('handles empty collection', function () {
    $source = collect([]);

    $exporter = new CsvExporter();
    $csv = $exporter
        ->source($source)
        ->columns([
            ExportColumn::make('name'),
            ExportColumn::make('email'),
        ])
        ->generate();

    $lines = array_filter(explode("\n", trim($csv)));

    // Only the header row should be present
    expect($lines)->toHaveCount(1);
    expect($lines[0])->toContain('Name');
});

it('formats state using ExportColumn callbacks', function () {
    $source = collect([
        (object) ['name' => 'alice', 'price' => 10],
    ]);

    $exporter = new CsvExporter();
    $csv = $exporter
        ->source($source)
        ->columns([
            ExportColumn::make('name')
                ->formatStateUsing(fn ($state) => strtoupper($state)),
            ExportColumn::make('price')
                ->formatStateUsing(fn ($state) => '$' . number_format($state, 2)),
        ])
        ->generate();

    $lines = array_filter(explode("\n", trim($csv)));

    expect($lines[1])->toContain('ALICE');
    expect($lines[1])->toContain('$10.00');
});

it('handles multiple rows correctly', function () {
    $source = collect([
        (object) ['id' => 1, 'name' => 'First'],
        (object) ['id' => 2, 'name' => 'Second'],
        (object) ['id' => 3, 'name' => 'Third'],
    ]);

    $exporter = new CsvExporter();
    $csv = $exporter
        ->source($source)
        ->columns([
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('name'),
        ])
        ->generate();

    $lines = array_filter(explode("\n", trim($csv)));

    // 1 header + 3 data rows
    expect($lines)->toHaveCount(4);
    expect($lines[1])->toContain('1');
    expect($lines[1])->toContain('First');
    expect($lines[2])->toContain('2');
    expect($lines[2])->toContain('Second');
    expect($lines[3])->toContain('3');
    expect($lines[3])->toContain('Third');
});
