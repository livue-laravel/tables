<?php

namespace Primix\Tables\Columns\Concerns;

use Primix\Tables\Columns\Summarizers\Summarizer;

trait HasSummarizers
{
    protected array $summarizers = [];

    public function summarize(Summarizer|array $summarizers): static
    {
        $this->summarizers = is_array($summarizers) ? $summarizers : [$summarizers];

        return $this;
    }

    /**
     * @return array<Summarizer>
     */
    public function getSummarizers(): array
    {
        return $this->summarizers;
    }

    public function hasSummarizers(): bool
    {
        return count($this->summarizers) > 0;
    }
}
