<?php

namespace Primix\Tables\Imports;

use Primix\Support\Components\ViewComponent;

class ImportPreview extends ViewComponent
{
    protected array $headers = [];

    protected array $rows = [];

    protected int $totalRows = 0;

    public function __construct()
    {
        //
    }

    public static function make(): static
    {
        return new static();
    }

    public function headers(array $headers): static
    {
        $this->headers = $headers;

        return $this;
    }

    public function rows(array $rows): static
    {
        $this->rows = $rows;

        return $this;
    }

    public function totalRows(int $count): static
    {
        $this->totalRows = $count;

        return $this;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getRows(): array
    {
        return $this->rows;
    }

    public function getTotalRows(): int
    {
        return $this->totalRows;
    }

    public function getView(): string
    {
        return 'primix-tables::imports.preview';
    }

    public function toVueProps(): array
    {
        return [
            'headers' => $this->headers,
            'rows' => $this->rows,
            'totalRows' => $this->totalRows,
        ];
    }
}
