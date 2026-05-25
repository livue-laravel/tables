<?php

/*
 * Regression: the empty-state icon in the tables view used to wrap the
 * SVG in a `<div class="mx-auto">`. mx-auto only horizontally-centres
 * elements that have an intrinsic or constrained width — applied to a
 * block div with no width it has no effect, so the icon rendered glued
 * to the left of the otherwise text-centred cell.
 *
 * The fix switches to `<div class="flex justify-center">`, which works
 * regardless of the icon's intrinsic size.
 *
 * These assertions read the Blade source directly so the test runs
 * without booting Laravel, IconManager or instantiating a Table.
 */

it('uses a flex-centred wrapper for the empty-state icon', function () {
    $blade = file_get_contents(__DIR__ . '/../resources/views/table.blade.php');

    expect($blade)
        ->toBeString()
        ->not->toBeEmpty();

    expect($blade)->toContain("<div class=\"flex justify-center\">");
});

it('does not regress to the mx-auto wrapper that did not center the icon', function () {
    $blade = file_get_contents(__DIR__ . '/../resources/views/table.blade.php');

    // The mx-auto pattern *somewhere else* in the template is fine; the
    // regression we want to prevent is the empty-state icon wrapper
    // sitting just before the IconManager render line.
    $needle = '<div class="mx-auto">' . "\n"
        . '                                    {!! app(\Primix\Support\Icons\IconManager::class)->render';

    expect($blade)->not->toContain($needle);
});
