<?php

use LiVue\Component;
use Primix\Tables\HasTable;
use Primix\Tables\Table;

it('initializes runtime table layout from table definition', function () {
    $component = new class extends Component
    {
        use HasTable;

        protected function table(Table $table): Table
        {
            return $table->grid(4)->switchableLayout();
        }

        protected function render(): string
        {
            return '';
        }
    };

    $table = $component->getTable();

    expect($component->tableLayout)->toBe('grid')
        ->and($table->getLayout())->toBe('grid');
});

it('can toggle table layout at runtime when switchable', function () {
    $component = new class extends Component
    {
        use HasTable;

        protected function table(Table $table): Table
        {
            return $table->switchableLayout();
        }

        protected function render(): string
        {
            return '';
        }
    };

    expect($component->getTable()->getLayout())->toBe('table');

    $component->toggleTableLayout();
    expect($component->getTable()->getLayout())->toBe('grid')
        ->and($component->tableLayout)->toBe('grid');

    $component->toggleTableLayout();
    expect($component->getTable()->getLayout())->toBe('table')
        ->and($component->tableLayout)->toBe('table');
});

it('does not toggle table layout when switchable layout is disabled', function () {
    $component = new class extends Component
    {
        use HasTable;

        protected function table(Table $table): Table
        {
            return $table;
        }

        protected function render(): string
        {
            return '';
        }
    };

    expect($component->getTable()->getLayout())->toBe('table');

    $component->toggleTableLayout();

    expect($component->getTable()->getLayout())->toBe('table')
        ->and($component->tableLayout)->toBe('table');
});

it('can set the table layout explicitly', function () {
    $component = new class extends Component
    {
        use HasTable;

        protected function table(Table $table): Table
        {
            return $table->switchableLayout();
        }

        protected function render(): string
        {
            return '';
        }
    };

    $component->getTable();
    $component->setTableLayout('grid');

    expect($component->getTable()->getLayout())->toBe('grid')
        ->and($component->tableLayout)->toBe('grid');
});

it('throws when setting an unsupported table layout', function () {
    $component = new class extends Component
    {
        use HasTable;

        protected function table(Table $table): Table
        {
            return $table->switchableLayout();
        }

        protected function render(): string
        {
            return '';
        }
    };

    $component->setTableLayout('kanban');
})->throws(\InvalidArgumentException::class);

