<?php

namespace Primix\Tables\Columns\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

trait CanUpdateState
{
    protected bool|Closure $isEditable = true;

    protected ?Closure $updateStateUsing = null;

    protected string|array|null $rules = null;

    protected array $validationMessages = [];

    protected ?Closure $afterStateUpdated = null;

    protected ?Closure $beforeStateUpdated = null;

    public function editable(bool|Closure $condition = true): static
    {
        $this->isEditable = $condition;

        return $this;
    }

    public function updateStateUsing(?Closure $callback): static
    {
        $this->updateStateUsing = $callback;

        return $this;
    }

    public function rules(string|array $rules): static
    {
        $this->rules = $rules;

        return $this;
    }

    public function validationMessages(array $messages): static
    {
        $this->validationMessages = $messages;

        return $this;
    }

    public function beforeStateUpdated(?Closure $callback): static
    {
        $this->beforeStateUpdated = $callback;

        return $this;
    }

    public function afterStateUpdated(?Closure $callback): static
    {
        $this->afterStateUpdated = $callback;

        return $this;
    }

    public function isEditable(): bool
    {
        return (bool) $this->evaluate($this->isEditable);
    }

    public function getRules(): string|array|null
    {
        return $this->rules;
    }

    public function getValidationMessages(): array
    {
        return $this->validationMessages;
    }

    /**
     * Execute the state update on the model record.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function updateState(Model $record, mixed $value): void
    {
        $this->currentRecord = $record;
        $this->currentState = $value;

        // Validate
        if ($this->rules !== null) {
            $validator = Validator::make(
                [$this->getName() => $value],
                [$this->getName() => $this->rules],
                $this->validationMessages,
            );

            $validator->validate();
        }

        // Before hook
        if ($this->beforeStateUpdated !== null) {
            $this->evaluate($this->beforeStateUpdated);
        }

        // Update
        if ($this->updateStateUsing !== null) {
            $this->evaluate($this->updateStateUsing);
        } else {
            $record->{$this->getName()} = $value;
            $record->save();
        }

        // After hook
        if ($this->afterStateUpdated !== null) {
            $this->evaluate($this->afterStateUpdated);
        }
    }
}
