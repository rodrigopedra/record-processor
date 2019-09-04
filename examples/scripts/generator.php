#!/usr/bin/env php
<?php

require __DIR__ . '/../../vendor/autoload.php';

use Illuminate\Support\Arr;
use RodrigoPedra\RecordProcessor\ProcessorBuilder;
use RodrigoPedra\RecordProcessor\Examples\RecordObjects\ExampleRecord;

$items = [
    ['Rodrigo', 'rodrigo@example.com', 'rodrigo@example.org'],
    ['Noemi', 'noemi@example.com'],
    ['Bruno', 'bruno@example.com', 'bruno@example.org'],
];

$processor = (new ProcessorBuilder)
    ->readFromArray($items)
    ->usingParser(function ($rawContent) {
        $name = Arr::get($rawContent, 0);

        foreach (range(1, 2) as $index) {
            $email = Arr::get($rawContent, $index) ?: false;

            if ($email === false) {
                return;
            }

            yield new ExampleRecord(compact('name', 'email'));
        }
    })
    ->writeToHTMLTable()
    ->build();

$output = $processor->process();

echo $output->getOutput(), PHP_EOL;

exit;
