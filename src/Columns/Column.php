<?php

namespace Primix\Tables\Columns;

use Closure;
use Primix\Support\Components\Schema\Component;
use Primix\Support\Concerns\HasColor;
use Primix\Support\Concerns\HasSchemaComponentIdentifier;
use Primix\Tables\Concerns\HasName;
use Primix\Tables\Columns\Concerns\CanBeCopied;
use Primix\Tables\Columns\Concerns\CanBeSearchable;
use Primix\Tables\Columns\Concerns\CanBeSortable;
use Primix\Tables\Columns\Concerns\HasAlignment;
use Primix\Tables\Columns\Concerns\HasSummarizers;
use Primix\Tables\Columns\Concerns\HasTooltip;

abstract class Column extends Component
{
    use CanBeCopied;
    use CanBeSearchable;
    use CanBeSortable;
    use HasAlignment;
    use HasColor;
    use HasName;
    use HasSchemaComponentIdentifier;
    use HasSummarizers;
    use HasTooltip;

    protected static ?string $schemaComponentCategory = 'column';

    protected ?string $evaluationIdentifier = 'column';

    protected ?Closure $formatStateUsing = null;

    protected ?Closure $getStateUsing = null;

    protected ?string $placeholder = null;

    protected bool|Closure $isToggleable = false;

    protected bool|Closure $isToggledHiddenByDefault = false;

    protected mixed $currentRecord = null;

    protected mixed $currentState = null;

    public function record(mixed $record): static
    {
        $this->currentRecord = $record;

        return $this;
    }

    public function getCurrentRecord(): mixed
    {
        return $this->currentRecord;
    }

    public function toHtml(): string
    {
        $innerHtml = view($this->getView(), array_merge(
            $this->toVueProps(),
            [
                'component' => $this,
                'record' => $this->currentRecord,
            ]
        ))->render();

        $wrapperView = $this->getWrapperView();

        if ($wrapperView) {
            return view($wrapperView, [
                'component' => $this,
                'slot' => $innerHtml,
            ])->render();
        }

        return $innerHtml;
    }

    public static function make(string $name): static
    {
        $instance = new static();
        $instance->name($name);
        $instance->label(str($name)->afterLast('.')->headline()->toString());
        $instance->configure();

        return $instance;
    }

    public function formatStateUsing(?Closure $callback): static
    {
        $this->formatStateUsing = $callback;

        return $this;
    }

    public function getStateUsing(?Closure $callback): static
    {
        $this->getStateUsing = $callback;

        return $this;
    }

    public function placeholder(?string $placeholder): static
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    public function toggleable(bool|Closure $condition = true, bool|Closure $isToggledHiddenByDefault = false): static
    {
        $this->isToggleable = $condition;
        $this->isToggledHiddenByDefault = $isToggledHiddenByDefault;

        return $this;
    }

    public function getState(mixed $record): mixed
    {
        $this->currentRecord = $record;

        if ($this->getStateUsing !== null) {
            $value = $this->evaluate($this->getStateUsing);
        } else {
            $value = data_get($record, $this->getName());
        }

        $this->currentState = $value;

        if ($this->formatStateUsing !== null) {
            $value = $this->evaluate($this->formatStateUsing);
        }

        return $value;
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'record' => [$this->currentRecord],
            'state', 'value' => [$this->currentState],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }

    public function getPlaceholder(): ?string
    {
        return $this->placeholder;
    }

    public function isToggleable(): bool
    {
        return (bool) $this->evaluate($this->isToggleable);
    }

    public function isToggledHiddenByDefault(): bool
    {
        return (bool) $this->evaluate($this->isToggledHiddenByDefault);
    }

    abstract public function getView(): string;

    public function toVueProps(): array
    {
        return array_merge(parent::toVueProps(), [
            'type' => class_basename(static::class),
            'name' => $this->getName(),
            'label' => $this->getLabel(),
            'sortable' => $this->isSortable(),
            'sortColumn' => $this->getSortColumn(),
            'searchable' => $this->isSearchable(),
            'individuallySearchable' => $this->isIndividuallySearchable(),
            'alignment' => $this->getAlignment(),
            'color' => $this->getColor(),
            'placeholder' => $this->getPlaceholder(),
            'toggleable' => $this->isToggleable(),
            'toggledHiddenByDefault' => $this->isToggledHiddenByDefault(),
            'copyable' => $this->isCopyable(),
            'tooltip' => $this->getTooltip(),
            'hidden' => $this->isHidden(),
            'width' => $this->getWidth(),
        ]);
    }
}
