<?php

namespace Primix\Tables\Actions;

use Primix\Actions\Action;

class AddAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'inline-create';
    }

    protected function setUp(): void
    {
        $this->label(__('primix-tables::tables.add'));
        $this->icon('pi pi-plus');
        $this->color('primary');
        $this->iconButton();
        $this->jsAction(
            "const row = \$event.target.closest('tr');" .
            "const data = {};" .
            "row.querySelectorAll('[data-inline-field]').forEach(el => {" .
            "    data[el.dataset.inlineField] = el.dataset.inlineFieldType === 'checkbox' ? el.checked : el.value;" .
            "});" .
            "createTableRecord([data])"
        );
    }
}
