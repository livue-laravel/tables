<?php

namespace Primix\Tables\Columns;

use Closure;
use Primix\Forms\Concerns\HasOptions;
use Primix\Tables\Columns\Concerns\CanUpdateState;
use Primix\Tables\Columns\Concerns\HasInlineStyle;

class SelectColumn extends Column
{
    use CanUpdateState;
    use HasOptions;
    use HasInlineStyle;

    protected string|Closure|null $selectPlaceholder = null;

    public function selectPlaceholder(string|Closure|null $placeholder): static
    {
        $this->selectPlaceholder = $placeholder;

        return $this;
    }

    public function getSelectPlaceholder(): ?string
    {
        return $this->evaluate($this->selectPlaceholder);
    }

    public function getView(): string
    {
        return 'primix-tables::columns.select-column';
    }

    public function toVueProps(): array
    {
        return array_merge(parent::toVueProps(), [
            'options' => $this->getOptions(),
            'editable' => $this->isEditable(),
            'selectPlaceholder' => $this->getSelectPlaceholder(),
            'inlineInput' => $this->isInlineInput(),
        ]);
    }
}
