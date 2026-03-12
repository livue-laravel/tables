<?php

namespace Primix\Tables\Columns;

use Primix\Tables\Columns\Concerns\CanUpdateState;
use Primix\Tables\Columns\Concerns\HasInlineStyle;

class CheckboxColumn extends Column
{
    use CanUpdateState;
    use HasInlineStyle;

    public function getView(): string
    {
        return 'primix-tables::columns.checkbox-column';
    }

    public function toVueProps(): array
    {
        return array_merge(parent::toVueProps(), [
            'editable' => $this->isEditable(),
            'inlineInput' => $this->isInlineInput(),
        ]);
    }
}
