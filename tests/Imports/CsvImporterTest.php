<?php

use Primix\Tables\Imports\CsvImporter;
use Primix\Tables\Imports\ImportColumn;

uses(Tests\TestCase::class);

function createTempCsv(string $content): string
{
    $path = tempnam(sys_get_temp_dir(), 'csv_test_');
    file_put_contents($path, $content);

    return $path;
}

afterEach(function () {
    // Clean up temp files created during tests
    foreach (glob(sys_get_temp_dir() . '/csv_test_*') as $file) {
        @unlink($file);
    }
});

it('parses CSV headers correctly', function () {
    $path = createTempCsv("name,email,age\nAlice,alice@example.com,30\nBob,bob@example.com,25\n");

    $importer = new CsvImporter();
    $result = $importer->parse($path);

    expect($result['headers'])->toBe(['name', 'email', 'age']);
});

it('parses preview rows limiting to 5 by default', function () {
    $lines = "id,name\n";
    for ($i = 1; $i <= 10; $i++) {
        $lines .= "{$i},User {$i}\n";
    }

    $path = createTempCsv($lines);

    $importer = new CsvImporter();
    $result = $importer->parse($path);

    expect($result['rows'])->toHaveCount(5);
    expect($result['rows'][0])->toBe(['1', 'User 1']);
    expect($result['rows'][4])->toBe(['5', 'User 5']);
});

it('counts total rows correctly', function () {
    $lines = "id,name\n";
    for ($i = 1; $i <= 10; $i++) {
        $lines .= "{$i},User {$i}\n";
    }

    $path = createTempCsv($lines);

    $importer = new CsvImporter();
    $result = $importer->parse($path);

    expect($result['totalRows'])->toBe(10);
});

it('handles custom delimiter in parse', function () {
    $path = createTempCsv("name;email\nAlice;alice@example.com\n");

    $importer = new CsvImporter();
    $result = $importer->delimiter(';')->parse($path);

    expect($result['headers'])->toBe(['name', 'email']);
    expect($result['rows'][0])->toBe(['Alice', 'alice@example.com']);
});

it('handles empty CSV file', function () {
    $path = createTempCsv('');

    $importer = new CsvImporter();
    $result = $importer->parse($path);

    expect($result['headers'])->toBe([]);
    expect($result['rows'])->toBe([]);
    expect($result['totalRows'])->toBe(0);
});

it('handles BOM in CSV', function () {
    $bom = "\xEF\xBB\xBF";
    $path = createTempCsv($bom . "name,email\nAlice,alice@example.com\n");

    $importer = new CsvImporter();
    $result = $importer->parse($path);

    expect($result['headers'][0])->toBe('name');
    expect($result['headers'][1])->toBe('email');
});

it('parses CSV with custom preview row count', function () {
    $lines = "id,name\n";
    for ($i = 1; $i <= 10; $i++) {
        $lines .= "{$i},User {$i}\n";
    }

    $path = createTempCsv($lines);

    $importer = new CsvImporter();
    $result = $importer->previewRows(3)->parse($path);

    expect($result['rows'])->toHaveCount(3);
    expect($result['totalRows'])->toBe(10);
});

it('import calls handleCreation callback for each row', function () {
    $path = createTempCsv("name,email\nAlice,alice@example.com\nBob,bob@example.com\n");

    $columns = [
        ImportColumn::make('name'),
        ImportColumn::make('email'),
    ];

    $headerMapping = [
        '0' => 'name',
        '1' => 'email',
    ];

    $createdRecords = [];
    $handleCreation = function (array $data) use (&$createdRecords) {
        $createdRecords[] = $data;
    };

    $importer = new CsvImporter();
    $result = $importer->import($path, $headerMapping, $columns, 'App\\Models\\User', $handleCreation);

    expect($result['created'])->toBe(2);
    expect($result['errors'])->toBe([]);
    expect($createdRecords)->toHaveCount(2);
    expect($createdRecords[0])->toBe(['name' => 'Alice', 'email' => 'alice@example.com']);
    expect($createdRecords[1])->toBe(['name' => 'Bob', 'email' => 'bob@example.com']);
});

it('import validates rows and collects errors', function () {
    $path = createTempCsv("name,email\n,alice@example.com\nBob,bob@example.com\n");

    $columns = [
        ImportColumn::make('name')->rules(['required']),
        ImportColumn::make('email'),
    ];

    $headerMapping = [
        '0' => 'name',
        '1' => 'email',
    ];

    $createdRecords = [];
    $handleCreation = function (array $data) use (&$createdRecords) {
        $createdRecords[] = $data;
    };

    $importer = new CsvImporter();
    $result = $importer->import($path, $headerMapping, $columns, 'App\\Models\\User', $handleCreation);

    expect($result['created'])->toBe(1);
    expect($result['errors'])->toHaveCount(1);
    expect($result['errors'][0]['row'])->toBe(2);
    expect($createdRecords)->toHaveCount(1);
    expect($createdRecords[0]['name'])->toBe('Bob');
});

it('import applies column defaults', function () {
    $path = createTempCsv("name\nAlice\n");

    $columns = [
        ImportColumn::make('name'),
        ImportColumn::make('status')->default('active'),
    ];

    $headerMapping = [
        '0' => 'name',
    ];

    $createdRecords = [];
    $handleCreation = function (array $data) use (&$createdRecords) {
        $createdRecords[] = $data;
    };

    $importer = new CsvImporter();
    $result = $importer->import($path, $headerMapping, $columns, 'App\\Models\\User', $handleCreation);

    expect($result['created'])->toBe(1);
    expect($createdRecords[0])->toBe(['name' => 'Alice', 'status' => 'active']);
});

it('import returns error count with row numbers', function () {
    $path = createTempCsv("name,email\n,\nBob,bob@example.com\n,\n");

    $columns = [
        ImportColumn::make('name')->rules(['required']),
        ImportColumn::make('email')->rules(['required', 'email']),
    ];

    $headerMapping = [
        '0' => 'name',
        '1' => 'email',
    ];

    $createdRecords = [];
    $handleCreation = function (array $data) use (&$createdRecords) {
        $createdRecords[] = $data;
    };

    $importer = new CsvImporter();
    $result = $importer->import($path, $headerMapping, $columns, 'App\\Models\\User', $handleCreation);

    expect($result['created'])->toBe(1);
    expect($result['errors'])->toHaveCount(2);
    expect($result['errors'][0]['row'])->toBe(2);
    expect($result['errors'][1]['row'])->toBe(4);
});
