<?php

namespace Primix\Tables\Actions;

use Closure;
use Primix\Actions\Action;
use Primix\Forms\Components\Fields\CheckboxList;
use Primix\Notifications\Notification;
use Primix\Support\Concerns\BelongsToLiVue;
use Primix\Tables\Exports\CsvExporter;
use Primix\Tables\Exports\ExportColumn;
use Primix\Tables\Exports\Exporter;

class ExportAction extends Action
{
    use BelongsToLiVue;
    /** @var array<ExportColumn>|Closure|null */
    protected array|Closure|null $exportColumns = null;

    protected string|Closure $fileName = '';

    protected string $delimiter = ',';

    protected bool $withHeader = true;

    protected ?Closure $modifyQueryUsing = null;

    /** @var class-string<Exporter>|null */
    protected ?string $exporterClass = null;

    public static function getDefaultName(): ?string
    {
        return 'export';
    }

    protected function setUp(): void
    {
        $this->label('Export');
        $this->icon('heroicon-o-arrow-down-tray');
        $this->color('gray');
        $this->modal();
        $this->modalHeading('Export Records');
        $this->modalDescription('Select the columns to include in the export.');
        $this->modalSubmitActionLabel('Export');
    }

    /**
     * @param array<ExportColumn>|Closure $columns
     */
    public function exportColumns(array|Closure $columns): static
    {
        $this->exportColumns = $columns;

        return $this;
    }

    public function fileName(string|Closure $name): static
    {
        $this->fileName = $name;

        return $this;
    }

    public function delimiter(string $delimiter): static
    {
        $this->delimiter = $delimiter;

        return $this;
    }

    public function withoutHeader(): static
    {
        $this->withHeader = false;

        return $this;
    }

    public function modifyQueryUsing(?Closure $callback): static
    {
        $this->modifyQueryUsing = $callback;

        return $this;
    }

    /**
     * @param class-string<Exporter> $exporterClass
     */
    public function exporter(string $exporterClass): static
    {
        $this->exporterClass = $exporterClass;

        return $this;
    }

    public function getExporterClass(): ?string
    {
        return $this->exporterClass;
    }

    /**
     * @return array<ExportColumn>
     */
    public function getExportColumns(): array
    {
        if ($this->exporterClass !== null) {
            return $this->exporterClass::getColumns();
        }

        if ($this->exportColumns !== null) {
            return $this->evaluate($this->exportColumns);
        }

        return $this->deriveExportColumnsFromTable();
    }

    public function getFileName(): string
    {
        if ($this->exporterClass !== null) {
            $exporterFileName = $this->exporterClass::getFileName();

            if ($exporterFileName !== null) {
                return str_ends_with($exporterFileName, '.csv') ? $exporterFileName : $exporterFileName . '.csv';
            }
        }

        $name = $this->evaluate($this->fileName);

        if ($name) {
            return str_ends_with($name, '.csv') ? $name : $name . '.csv';
        }

        return 'export-' . now()->format('Y-m-d-His') . '.csv';
    }

    public function getFormSchema(): array
    {
        $columns = $this->getExportColumns();
        $options = [];

        foreach ($columns as $column) {
            $options[$column->getName()] = $column->getLabel();
        }

        return [
            CheckboxList::make('columns')
                ->label('Columns')
                ->options($options)
                ->default(array_keys($options))
                ->bulkToggleable(),
        ];
    }

    public function getFormData(): array
    {
        $columns = $this->getExportColumns();

        return [
            'columns' => array_map(fn (ExportColumn $col) => $col->getName(), $columns),
        ];
    }

    public function call(array $data = []): mixed
    {
        $selectedColumnNames = $data['columns'] ?? [];

        $allColumns = $this->getExportColumns();
        $selectedColumns = array_filter(
            $allColumns,
            fn (ExportColumn $col) => in_array($col->getName(), $selectedColumnNames),
        );

        if (empty($selectedColumns)) {
            return null;
        }

        $livue = $this->getLiVue();
        $query = $livue->getFilteredTableQuery();

        $modifyQuery = $this->exporterClass !== null
            ? $this->exporterClass::modifyQueryUsing()
            : $this->modifyQueryUsing;

        if ($modifyQuery !== null) {
            $query = $this->evaluate($modifyQuery, [
                'query' => $query,
            ]) ?? $query;
        }

        $delimiter = $this->exporterClass !== null
            ? $this->exporterClass::getDelimiter()
            : $this->delimiter;

        $withHeader = $this->exporterClass !== null
            ? $this->exporterClass::withHeader()
            : $this->withHeader;

        $exporter = new CsvExporter();
        $exporter->source($query)
            ->columns(array_values($selectedColumns))
            ->delimiter($delimiter);

        if (! $withHeader) {
            $exporter->withoutHeader();
        }

        $csv = $exporter->generate();
        $livue->downloadContent($csv, $this->getFileName());

        Notification::make()
            ->title('Export completed')
            ->success()
            ->send();

        return null;
    }

    /**
     * @return array<ExportColumn>
     */
    protected function deriveExportColumnsFromTable(): array
    {
        $livue = $this->getLiVue();

        if ($livue === null || ! method_exists($livue, 'getTable')) {
            return [];
        }

        $table = $livue->getTable();
        $columns = [];

        foreach ($table->getColumns() as $column) {
            $columns[] = ExportColumn::make($column->getName())
                ->label($column->getLabel());
        }

        return $columns;
    }
}
