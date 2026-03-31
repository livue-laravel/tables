<?php

namespace Primix\Tables\Columns;

class ViewColumn extends Column
{
    protected ?string $customView = null;

    public function view(string $view): static
    {
        $this->customView = $view;

        return $this;
    }

    public function getView(): string
    {
        return $this->customView ?? '';
    }

    public function toVueProps(): array
    {
        return array_merge(parent::toVueProps(), [
            'state' => $this->getState($this->currentRecord),
        ]);
    }
}
