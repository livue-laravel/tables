<?php

namespace Primix\Tables\Imports;

use Illuminate\Support\Facades\Validator;

class CsvImporter
{
    protected string $delimiter = ',';

    protected string $enclosure = '"';

    protected int $previewRows = 5;

    public function delimiter(string $delimiter): static
    {
        $this->delimiter = $delimiter;

        return $this;
    }

    public function enclosure(string $enclosure): static
    {
        $this->enclosure = $enclosure;

        return $this;
    }

    public function previewRows(int $count): static
    {
        $this->previewRows = $count;

        return $this;
    }

    /**
     * Parse a CSV file and return headers, preview rows, and total row count.
     *
     * @return array{headers: array, rows: array, totalRows: int}
     */
    public function parse(string $filePath): array
    {
        $handle = fopen($filePath, 'r');

        if ($handle === false) {
            return ['headers' => [], 'rows' => [], 'totalRows' => 0];
        }

        $headers = fgetcsv($handle, 0, $this->delimiter, $this->enclosure, '\\');

        if ($headers === false) {
            fclose($handle);

            return ['headers' => [], 'rows' => [], 'totalRows' => 0];
        }

        // Remove BOM if present
        if (! empty($headers[0])) {
            $headers[0] = preg_replace('/^\x{FEFF}/u', '', $headers[0]);
        }

        $rows = [];
        $totalRows = 0;

        while (($row = fgetcsv($handle, 0, $this->delimiter, $this->enclosure, '\\')) !== false) {
            $totalRows++;

            if (count($rows) < $this->previewRows) {
                $rows[] = $row;
            }
        }

        fclose($handle);

        return [
            'headers' => $headers,
            'rows' => $rows,
            'totalRows' => $totalRows,
        ];
    }

    /**
     * Import records from a CSV file.
     *
     * @param  string              $filePath       Path to the CSV file
     * @param  array<string, string> $headerMapping  Maps CSV column index => ImportColumn name
     * @param  array<ImportColumn> $importColumns  The import column definitions
     * @param  string              $modelClass     The Eloquent model class
     * @param  callable|null       $handleCreation Custom creation callback
     * @return array{created: int, errors: array}
     */
    public function import(
        string $filePath,
        array $headerMapping,
        array $importColumns,
        string $modelClass,
        ?callable $handleCreation = null,
    ): array {
        $handle = fopen($filePath, 'r');

        if ($handle === false) {
            return ['created' => 0, 'errors' => [['row' => 0, 'message' => 'Could not open file.']]];
        }

        // Skip header row
        fgetcsv($handle, 0, $this->delimiter, $this->enclosure, '\\');

        // Index import columns by name
        $columnsByName = [];
        foreach ($importColumns as $column) {
            $columnsByName[$column->getName()] = $column;
        }

        $created = 0;
        $errors = [];
        $rowNumber = 1;

        while (($row = fgetcsv($handle, 0, $this->delimiter, $this->enclosure, '\\')) !== false) {
            $rowNumber++;

            $data = [];
            $rules = [];

            foreach ($headerMapping as $csvIndex => $columnName) {
                if ($columnName === '' || $columnName === null) {
                    continue;
                }

                $column = $columnsByName[$columnName] ?? null;

                if ($column === null) {
                    continue;
                }

                $rawValue = $row[(int) $csvIndex] ?? null;
                $data[$columnName] = $column->resolveValue($rawValue);

                if (! empty($column->getRules())) {
                    $rules[$columnName] = $column->getRules();
                }
            }

            // Apply defaults for columns not in CSV
            foreach ($importColumns as $column) {
                if (! array_key_exists($column->getName(), $data) && $column->hasDefault()) {
                    $data[$column->getName()] = $column->getDefault();
                }
            }

            if (! empty($rules)) {
                $validator = Validator::make($data, $rules);

                if ($validator->fails()) {
                    $messages = $validator->errors()->all();
                    $errors[] = [
                        'row' => $rowNumber,
                        'message' => implode(', ', $messages),
                    ];

                    continue;
                }
            }

            try {
                if ($handleCreation !== null) {
                    $handleCreation($data);
                } else {
                    $modelClass::create($data);
                }

                $created++;
            } catch (\Throwable $e) {
                $errors[] = [
                    'row' => $rowNumber,
                    'message' => $e->getMessage(),
                ];
            }
        }

        fclose($handle);

        return [
            'created' => $created,
            'errors' => $errors,
        ];
    }
}
