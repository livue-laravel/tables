<?php

it('table row click template sets livue navigate only when spa is enabled', function () {
    $template = file_get_contents(dirname(__DIR__) . '/resources/views/table.blade.php');

    expect($template)
        ->toContain("\$spaEnabled = (bool) (\$spa ?? false);")
        ->toContain("@if(\$spaEnabled)")
        ->toContain("link.setAttribute('data-livue-navigate', 'true');");
});

it('table template renders bulk bar actions through table API without hardcoded button', function () {
    $template = file_get_contents(dirname(__DIR__) . '/resources/views/table.blade.php');

    expect($template)
        ->toContain('@foreach($table->getBulkBarActions() as $bulkBarAction)')
        ->not->toContain('<button');
});

it('text column link template sets livue navigate only when spa is enabled', function () {
    $template = file_get_contents(dirname(__DIR__) . '/resources/views/columns/text-column.blade.php');

    expect($template)
        ->toContain("\$spaEnabled = (bool) (\$spa ?? false);")
        ->toContain("@if(\$spaEnabled && ! \$openUrlInNewTab) data-livue-navigate=\"true\" @endif");
});

it('column toggle template renders actions through table API without hardcoded button', function () {
    $template = file_get_contents(dirname(__DIR__) . '/resources/views/columns/column-toggle.blade.php');

    expect($template)
        ->toContain('@foreach($table->getColumnToggleActions() as $columnToggleAction)')
        ->not->toContain('<button');
});

it('table template renders layout toggle actions through table API without hardcoded button', function () {
    $template = file_get_contents(dirname(__DIR__) . '/resources/views/table.blade.php');

    expect($template)
        ->toContain('$layoutToggleActions = $table->getLayoutToggleActions();')
        ->toContain('@foreach($layoutToggleActions as $layoutToggleAction)')
        ->not->toContain('<button');
});
