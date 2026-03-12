<?php

namespace Primix\Tables\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class MakeFilterCommand extends GeneratorCommand
{
    protected $signature = 'make:primix-filter
                            {name : Filter name}
                            {--select : Generate a SelectFilter}
                            {--date : Generate a DateFilter}
                            {--boolean : Generate a BooleanFilter}
                            {--ternary : Generate a TernaryFilter}
                            {--trashed : Generate a TrashedFilter}';

    protected $description = 'Create a new Primix table filter class';

    protected $type = 'Filter';

    protected $aliases = ['primix:filter'];

    public function handle(): ?bool
    {
        if ($this->hasMultipleTypeOptions()) {
            $this->components->error('Use only one type option among --select, --date, --boolean, --ternary, --trashed.');

            return false;
        }

        return parent::handle();
    }

    protected function getStub(): string
    {
        return __DIR__ . '/../../stubs/filter-' . $this->getFilterType() . '.stub';
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\\Primix\\Tables\\Filters';
    }

    protected function getNameInput(): string
    {
        return $this->getFilterClassName();
    }

    protected function buildClass($name): string
    {
        $stub = parent::buildClass($name);

        return str_replace(
            ['{{ label }}'],
            [$this->getFilterLabel()],
            $stub,
        );
    }

    protected function getFilterClassName(): string
    {
        $className = Str::studly((string) $this->argument('name'));

        if (! Str::endsWith($className, 'Filter')) {
            $className .= 'Filter';
        }

        return $className;
    }

    protected function getFilterLabel(): string
    {
        return Str::of(Str::beforeLast($this->getFilterClassName(), 'Filter'))
            ->headline()
            ->toString();
    }

    protected function getFilterType(): string
    {
        if ($this->option('date')) {
            return 'date';
        }

        if ($this->option('boolean')) {
            return 'boolean';
        }

        if ($this->option('ternary')) {
            return 'ternary';
        }

        if ($this->option('trashed')) {
            return 'trashed';
        }

        return 'select';
    }

    protected function hasMultipleTypeOptions(): bool
    {
        $selected = [
            $this->option('select'),
            $this->option('date'),
            $this->option('boolean'),
            $this->option('ternary'),
            $this->option('trashed'),
        ];

        return count(array_filter($selected)) > 1;
    }

    protected function rootNamespace(): string
    {
        if (method_exists($this->laravel, 'getNamespace')) {
            return $this->laravel->getNamespace();
        }

        return 'App\\';
    }
}
