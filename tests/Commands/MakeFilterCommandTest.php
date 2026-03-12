<?php

use Primix\Tables\Commands\MakeFilterCommand;
use Illuminate\Config\Repository;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\ArrayInput;

function callMakeFilterProtected(MakeFilterCommand $command, string $method, array $arguments = []): mixed
{
    $reflection = new ReflectionClass($command);
    $methodRef = $reflection->getMethod($method);
    $methodRef->setAccessible(true);

    return $methodRef->invokeArgs($command, $arguments);
}

function buildMakeFilterCommand(array $input): MakeFilterCommand
{
    $command = new MakeFilterCommand(new Filesystem());

    $app = app();

    if (! $app->bound('config')) {
        $app->instance('config', new Repository([
            'auth' => [
                'defaults' => ['guard' => 'web'],
                'guards' => ['web' => ['provider' => 'users']],
                'providers' => ['users' => ['model' => 'App\\Models\\User']],
            ],
        ]));
    }

    $command->setLaravel($app);

    $arrayInput = new ArrayInput($input, $command->getDefinition());

    $reflection = new ReflectionClass($command);
    $property = $reflection->getProperty('input');
    $property->setAccessible(true);
    $property->setValue($command, $arrayInput);

    return $command;
}

it('normalizes filter class name and default label', function () {
    $command = buildMakeFilterCommand([
        'name' => 'status',
    ]);

    expect(callMakeFilterProtected($command, 'getNameInput'))->toBe('StatusFilter')
        ->and(callMakeFilterProtected($command, 'getFilterLabel'))->toBe('Status')
        ->and(callMakeFilterProtected($command, 'getFilterType'))->toBe('select');
});

it('selects correct filter type based on option', function (array $input, string $expectedType) {
    $command = buildMakeFilterCommand($input);

    expect(callMakeFilterProtected($command, 'getFilterType'))->toBe($expectedType);
})->with([
    'select (default)' => [['name' => 'Status'], 'select'],
    'date' => [['name' => 'Created At', '--date' => true], 'date'],
    'boolean' => [['name' => 'Published', '--boolean' => true], 'boolean'],
    'ternary' => [['name' => 'Archived', '--ternary' => true], 'ternary'],
    'trashed' => [['name' => 'Trashed', '--trashed' => true], 'trashed'],
]);

it('builds selected filter stub variant', function () {
    $command = buildMakeFilterCommand([
        'name' => 'published',
        '--boolean' => true,
    ]);

    $stub = callMakeFilterProtected($command, 'buildClass', ['App\\Primix\\Tables\\Filters\\PublishedFilter']);

    expect($stub)->toContain('class PublishedFilter extends BooleanFilter')
        ->and($stub)->toContain("\$this->label('Published');");
});
