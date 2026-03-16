<?php

namespace Primix\Tables\Concerns;

use Closure;
use Primix\Actions\Action;

trait HasTableActions
{
    protected array $actions = [];

    protected array $bulkActions = [];

    protected array $headerActions = [];

    protected array|Closure|null $bulkBarActions = null;

    protected array|Closure|null $columnToggleActions = null;

    protected array|Closure|null $layoutToggleActions = null;

    protected ?string $resourceClass = null;

    public function resource(?string $resourceClass): static
    {
        $this->resourceClass = $resourceClass;

        $this->propagateResourceTo($this->actions);
        $this->propagateResourceTo($this->headerActions);
        $this->propagateResourceTo($this->bulkActions);

        return $this;
    }

    public function getResourceClass(): ?string
    {
        return $this->resourceClass;
    }

    public function actions(array $actions): static
    {
        $this->actions = $actions;
        $this->propagateContextToComponents($this->actions);
        $this->propagateResourceTo($this->actions);

        return $this;
    }

    public function bulkActions(array $actions): static
    {
        $this->bulkActions = $actions;
        $this->propagateContextToComponents($this->bulkActions);
        $this->propagateResourceTo($this->bulkActions);

        return $this;
    }

    public function headerActions(array $actions): static
    {
        $this->headerActions = $actions;
        $this->propagateContextToComponents($this->headerActions);
        $this->propagateResourceTo($this->headerActions);

        return $this;
    }

    public function bulkBarActions(array|Closure $actions): static
    {
        $this->bulkBarActions = $actions;

        return $this;
    }

    public function getBulkBarActions(): array
    {
        if ($this->bulkBarActions === null) {
            return $this->getDefaultBulkBarActions();
        }

        return $this->filterVisibleActions($this->resolveConfiguredActions($this->bulkBarActions));
    }

    public function columnToggleActions(array|Closure $actions): static
    {
        $this->columnToggleActions = $actions;

        return $this;
    }

    public function getColumnToggleActions(): array
    {
        if ($this->columnToggleActions === null) {
            return $this->getDefaultColumnToggleActions();
        }

        return $this->filterVisibleActions($this->resolveConfiguredActions($this->columnToggleActions));
    }

    public function layoutToggleActions(array|Closure $actions): static
    {
        $this->layoutToggleActions = $actions;

        return $this;
    }

    public function getLayoutToggleActions(): array
    {
        if (! $this->isLayoutSwitchable()) {
            return [];
        }

        if ($this->layoutToggleActions === null) {
            return $this->getDefaultLayoutToggleActions();
        }

        return $this->filterVisibleActions($this->resolveConfiguredActions($this->layoutToggleActions));
    }

    public function getActions(): array
    {
        return $this->actions;
    }

    public function getVisibleActions(): array
    {
        return array_filter($this->actions, fn ($action) => ! $action->isHidden());
    }

    public function getRowActions(): array
    {
        return array_values(array_filter(
            $this->actions,
            fn ($action) => ! ($action instanceof \Primix\Tables\Actions\AddAction)
        ));
    }

    public function getBulkActions(): array
    {
        return $this->bulkActions;
    }

    public function getVisibleBulkActions(): array
    {
        return array_filter($this->bulkActions, fn ($action) => ! $action->isHidden());
    }

    public function hasBulkActions(): bool
    {
        return count($this->getVisibleBulkActions()) > 0;
    }

    public function getHeaderActions(): array
    {
        return $this->headerActions;
    }

    protected function propagateResourceTo(array $items): void
    {
        if ($this->resourceClass === null) {
            return;
        }

        foreach ($items as $item) {
            if (method_exists($item, 'resource')) {
                $item->resource($this->resourceClass);
            }
        }
    }

    protected function resolveConfiguredActions(array|Closure $actions): array
    {
        $resolved = $actions instanceof Closure
            ? $this->evaluate($actions)
            : $actions;

        if (! is_array($resolved)) {
            return [];
        }

        $this->propagateContextToComponents($resolved);
        $this->propagateResourceTo($resolved);

        return $resolved;
    }

    protected function filterVisibleActions(array $actions): array
    {
        return array_values(array_filter($actions, function ($action) {
            if (! method_exists($action, 'isHidden')) {
                return true;
            }

            return ! $action->isHidden();
        }));
    }

    protected function getDefaultBulkBarActions(): array
    {
        return $this->resolveConfiguredActions([
            Action::make('clearSelection')
                ->label(__('primix-tables::tables.cancel'))
                ->link()
                ->color('gray')
                ->jsAction("livue.set('selectedRecords', [])")
                ->extraAttributes([
                    'class' => 'ml-auto text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300',
                ]),
        ]);
    }

    protected function getDefaultColumnToggleActions(): array
    {
        return $this->resolveConfiguredActions([
            Action::make('toggleColumns')
                ->label(__('primix-tables::tables.toggle_columns'))
                ->icon('pi pi-table')
                ->iconButton(true, true)
                ->color('gray')
                ->jsAction('columnTogglePopover.toggle($event)')
                ->extraAttributes([
                    'class' => 'inline-flex items-center justify-center rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 p-1.5 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700',
                ]),
        ]);
    }

    protected function getDefaultLayoutToggleActions(): array
    {
        return $this->resolveConfiguredActions([
            Action::make('toggleLayout')
                ->label(fn () => $this->getLayout() === 'grid'
                    ? __('primix-tables::tables.switch_to_table')
                    : __('primix-tables::tables.switch_to_grid')
                )
                ->icon(fn () => $this->getLayout() === 'grid' ? 'pi pi-table' : 'pi pi-th-large')
                ->iconButton(true, true)
                ->color('gray')
                ->jsAction('toggleTableLayout()')
                ->extraAttributes([
                    'class' => 'inline-flex items-center justify-center rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 p-1.5 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700',
                ]),
        ]);
    }
}
