<?php

namespace Primix\Tables\Actions;

use Closure;
use Primix\Actions\Action;
use Primix\Forms\Components\Fields\FileUpload;
use Primix\Forms\Components\Fields\Select;
use Primix\Notifications\Notification;
use Primix\Support\Concerns\BelongsToLiVue;
use LiVue\Features\SupportFileUploads\TemporaryUploadedFile;
use Primix\Tables\Imports\CsvImporter;
use Primix\Tables\Imports\ImportColumn;
use Primix\Tables\Imports\Importer;
use Primix\Tables\Imports\ImportPreview;

class ImportAction extends Action
{
    use BelongsToLiVue;
    /** @var array<ImportColumn> */
    protected array $importColumns = [];

    protected string $delimiter = ',';

    protected ?Closure $handleRecordCreation = null;

    protected ?Closure $beforeImport = null;

    protected ?Closure $afterImport = null;

    /** @var class-string<Importer>|null */
    protected ?string $importerClass = null;

    public static function getDefaultName(): ?string
    {
        return 'import';
    }

    protected function setUp(): void
    {
        $this->label(__('primix-tables::tables.import'));
        $this->icon('heroicon-o-arrow-up-tray');
        $this->color('gray');
        $this->modal();
        $this->modalHeading(__('primix-tables::tables.import_records'));
        $this->modalWidth('lg');
    }

    /**
     * @param array<ImportColumn> $columns
     */
    public function importColumns(array $columns): static
    {
        $this->importColumns = $columns;

        return $this;
    }

    public function delimiter(string $delimiter): static
    {
        $this->delimiter = $delimiter;

        return $this;
    }

    public function handleRecordCreation(?Closure $callback): static
    {
        $this->handleRecordCreation = $callback;

        return $this;
    }

    public function beforeImport(?Closure $callback): static
    {
        $this->beforeImport = $callback;

        return $this;
    }

    public function afterImport(?Closure $callback): static
    {
        $this->afterImport = $callback;

        return $this;
    }

    /**
     * @param class-string<Importer> $importerClass
     */
    public function importer(string $importerClass): static
    {
        $this->importerClass = $importerClass;

        return $this;
    }

    public function getImporterClass(): ?string
    {
        return $this->importerClass;
    }

    /**
     * @return array<ImportColumn>
     */
    public function getImportColumns(): array
    {
        if ($this->importerClass !== null) {
            return $this->importerClass::getColumns();
        }

        return $this->importColumns;
    }

    public function getFormSchema(): array
    {
        $livue = $this->getLiVue();
        $step = $livue?->mountedActionData['_step'] ?? 1;

        if ($step === 2) {
            return $this->getStep2FormSchema();
        }

        return $this->getStep1FormSchema();
    }

    protected function getStep1FormSchema(): array
    {
        return [
            FileUpload::make('file')
                ->label(__('primix-tables::tables.csv_file'))
                ->acceptedFileTypes(['text/csv', 'text/plain', 'application/csv', 'application/vnd.ms-excel'])
                ->required()
                ->disk('local')
                ->directory('imports'),
        ];
    }

    protected function getStep2FormSchema(): array
    {
        $livue = $this->getLiVue();
        $data = $livue?->mountedActionData ?? [];

        $headers = $data['_headers'] ?? [];
        $previewRows = $data['_preview'] ?? [];
        $totalRows = $data['_totalRows'] ?? 0;

        $csvHeaderOptions = [];
        foreach ($headers as $index => $header) {
            $csvHeaderOptions[(string) $index] = $header;
        }

        $components = [];

        // Add the preview component
        $components[] = ImportPreview::make()
            ->headers($headers)
            ->rows($previewRows)
            ->totalRows($totalRows);

        // Add a select for each import column
        foreach ($this->getImportColumns() as $column) {
            $defaultMapping = $data['_mapping'][$column->getName()] ?? null;

            $components[] = Select::make('mapping_' . $column->getName())
                ->label(__('primix-tables::tables.map_column_to', ['column' => $column->getLabel()]))
                ->options($csvHeaderOptions)
                ->default($defaultMapping);
        }

        return $components;
    }

    public function call(array $data = []): mixed
    {
        $step = $data['_step'] ?? 1;

        if ($step === 1) {
            return $this->handleStep1($data);
        }

        return $this->handleStep2($data);
    }

    protected function handleStep1(array $data): mixed
    {
        $file = $data['file'] ?? null;

        if (! $file) {
            return null;
        }

        // Handle TemporaryUploadedFile from LiVue upload system
        if ($file instanceof TemporaryUploadedFile) {
            $filePath = $file->store('imports', 'local');
        } elseif (is_string($file)) {
            $filePath = $file;
        } else {
            return null;
        }

        $fullPath = storage_path('app/private/' . $filePath);

        if (! file_exists($fullPath)) {
            $fullPath = storage_path('app/' . $filePath);
        }

        if (! file_exists($fullPath)) {
            Notification::make()
                ->title(__('primix-tables::tables.import_error_unreadable'))
                ->danger()
                ->send();

            return null;
        }

        $delimiter = $this->importerClass !== null
            ? $this->importerClass::getDelimiter()
            : $this->delimiter;

        $importer = new CsvImporter();
        $importer->delimiter($delimiter);

        $parsed = $importer->parse($fullPath);

        if (empty($parsed['headers'])) {
            Notification::make()
                ->title(__('primix-tables::tables.import_error_empty'))
                ->danger()
                ->send();

            return null;
        }

        // Auto-map: try to match CSV headers to import columns by normalized name
        $mapping = [];
        foreach ($this->getImportColumns() as $column) {
            $normalizedColumnName = str($column->getName())->lower()->replace('_', ' ')->toString();

            foreach ($parsed['headers'] as $index => $header) {
                $normalizedHeader = str($header)->lower()->trim()->toString();

                if ($column->getMapFrom() !== null) {
                    if (str($column->getMapFrom())->lower()->trim()->toString() === $normalizedHeader) {
                        $mapping[$column->getName()] = (string) $index;
                        break;
                    }
                } elseif ($normalizedColumnName === $normalizedHeader) {
                    $mapping[$column->getName()] = (string) $index;
                    break;
                }
            }
        }

        // Update mounted action data for step 2
        $livue = $this->getLiVue();
        $livue->mountedActionData = array_merge($data, [
            '_step' => 2,
            '_headers' => $parsed['headers'],
            '_preview' => $parsed['rows'],
            '_totalRows' => $parsed['totalRows'],
            '_file' => $filePath,
            '_mapping' => $mapping,
        ]);

        // Pre-fill select defaults
        foreach ($mapping as $columnName => $csvIndex) {
            $livue->mountedActionData['mapping_' . $columnName] = $csvIndex;
        }

        return Action::HALT;
    }

    protected function handleStep2(array $data): mixed
    {
        $filePath = $data['_file'] ?? null;

        if (! $filePath) {
            return null;
        }

        $fullPath = storage_path('app/private/' . $filePath);

        if (! file_exists($fullPath)) {
            $fullPath = storage_path('app/' . $filePath);
        }

        // Build header mapping: csv column index => import column name
        $importColumns = $this->getImportColumns();
        $headerMapping = [];
        foreach ($importColumns as $column) {
            $mappedIndex = $data['mapping_' . $column->getName()] ?? '';

            if ($mappedIndex !== '' && $mappedIndex !== null) {
                $headerMapping[$mappedIndex] = $column->getName();
            }
        }

        if (empty($headerMapping)) {
            Notification::make()
                ->title(__('primix-tables::tables.import_error_no_columns_mapped'))
                ->warning()
                ->send();

            return null;
        }

        // Execute before hook
        if ($this->importerClass !== null) {
            $this->importerClass::beforeImport();
        } elseif ($this->beforeImport !== null) {
            $this->evaluate($this->beforeImport);
        }

        // Resolve model class from resource
        $modelClass = $this->resolveModelClass();

        $delimiter = $this->importerClass !== null
            ? $this->importerClass::getDelimiter()
            : $this->delimiter;

        $handleRecordCreation = $this->importerClass !== null
            ? $this->importerClass::getHandleRecordCreation()
            : $this->handleRecordCreation;

        $importer = new CsvImporter();
        $importer->delimiter($delimiter);

        $result = $importer->import(
            $fullPath,
            $headerMapping,
            $importColumns,
            $modelClass,
            $handleRecordCreation,
        );

        // Clean up uploaded file
        @unlink($fullPath);

        // Execute after hook
        if ($this->importerClass !== null) {
            $this->importerClass::afterImport($result['created'], $result['errors']);
        } elseif ($this->afterImport !== null) {
            $this->evaluate($this->afterImport, [
                'created' => $result['created'],
                'errors' => $result['errors'],
            ]);
        }

        // Send notification
        $errorCount = count($result['errors']);

        if ($errorCount === 0) {
            Notification::make()
                ->title("{$result['created']} record(s) imported successfully")
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title("{$result['created']} imported, {$errorCount} error(s)")
                ->body('Rows with errors: ' . implode(', ', array_map(fn ($e) => "#{$e['row']}", $result['errors'])))
                ->warning()
                ->send();
        }

        return null;
    }

    protected function resolveModelClass(): string
    {
        if ($this->importerClass !== null) {
            $model = $this->importerClass::getModel();

            if ($model !== null) {
                return $model;
            }
        }

        $resource = $this->getResourceClass();

        if ($resource !== null && method_exists($resource, 'getModel')) {
            return $resource::getModel();
        }

        // Fallback: try to get from LiVue component's table
        $livue = $this->getLiVue();

        if ($livue !== null && method_exists($livue, 'getTable')) {
            $query = $livue->getTable()->getQuery();

            if ($query !== null) {
                return get_class($query->getModel());
            }
        }

        throw new \RuntimeException('Cannot resolve model class for import. Please set a resource or provide a handleRecordCreation callback.');
    }
}
