<?php

use Primix\Tables\Table;

it('has no record title attribute by default', function () {
    expect(Table::make()->getRecordTitleAttribute())->toBeNull();
});

it('can set the record title attribute explicitly', function () {
    $table = Table::make()->recordTitleAttribute('name');

    expect($table->getRecordTitleAttribute())->toBe('name');
});

it('falls back to the linked resource record title attribute', function () {
    $resource = new class
    {
        public static function getRecordTitleAttribute(): ?string
        {
            return 'title';
        }
    };

    $table = Table::make()->resource($resource::class);

    expect($table->getRecordTitleAttribute())->toBe('title');
});

it('prefers the explicit attribute over the resource one', function () {
    $resource = new class
    {
        public static function getRecordTitleAttribute(): ?string
        {
            return 'title';
        }
    };

    $table = Table::make()->resource($resource::class)->recordTitleAttribute('name');

    expect($table->getRecordTitleAttribute())->toBe('name');
});
