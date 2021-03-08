#!/usr/bin/env php
<?php

require __DIR__ . '/../../vendor/autoload.php';

use Illuminate\Support\Arr;
use RodrigoPedra\RecordProcessor\Examples\RecordObjects\ExampleRecord;
use RodrigoPedra\RecordProcessor\ProcessorBuilder;

$items = [
    ['Rodrigo', 'rodrigo@example.com', 'rodrigo@example.org'],
    ['Noemi', 'noemi@example.com'],
    ['Bruno', 'bruno@example.com', 'bruno@example.org'],
];

$processor = (new ProcessorBuilder())
    ->readFromArray($items)
    ->withRecordParser(function ($rawContent) {
        $name = Arr::get($rawContent, 0);

        foreach (\range(1, 2) as $index) {
            $email = Arr::get($rawContent, $index) ?: false;

            if ($email === false) {
                return;
            }

            yield new ExampleRecord($email, [
                'name' => $name,
                'email' => $email,
            ]);
        }
    })
    ->serializeToHTMLTable()
    ->build();

$output = $processor->process();

echo $output->output(), \PHP_EOL;

exit;
