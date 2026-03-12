<?php

namespace Primix\Tables\Exports;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class CsvExporter
{
    protected Builder|Collection|null $source = null;

    /** @var array<ExportColumn> */
    protected array $columns = [];

    protected string $delimiter = ',';

    protected string $enclosure = '"';

    protected bool $withHeader = true;

    protected int $chunkSize = 1000;

    public function source(Builder|Collection $source): static
    {
        $this->source = $source;

        return $this;
    }

    /**
     * @param array<ExportColumn> $columns
     */
    public function columns(array $columns): static
    {
        $this->columns = $columns;

        return $this;
    }

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

    public function withoutHeader(): static
    {
        $this->withHeader = false;

        return $this;
    }

    public function chunkSize(int $size): static
    {
        $this->chunkSize = $size;

        return $this;
    }

    public function generate(): string
    {
        $handle = fopen('php://memory', 'r+');

        if ($this->withHeader) {
            fputcsv(
                $handle,
                array_map(fn (ExportColumn $col) => $col->getLabel(), $this->columns),
                $this->delimiter,
                $this->enclosure,
                '\\',
            );
        }

        if ($this->source instanceof Builder) {
            $this->source->chunk($this->chunkSize, function ($records) use ($handle) {
                foreach ($records as $record) {
                    $this->writeRow($handle, $record);
                }
            });
        } elseif ($this->source instanceof Collection) {
            foreach ($this->source as $record) {
                $this->writeRow($handle, $record);
            }
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return $content;
    }

    protected function writeRow($handle, mixed $record): void
    {
        $row = array_map(
            fn (ExportColumn $col) => $col->getState($record),
            $this->columns,
        );

        fputcsv($handle, $row, $this->delimiter, $this->enclosure, '\\');
    }
}
