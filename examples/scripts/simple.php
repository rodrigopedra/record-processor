#!/usr/bin/env php
<?php

require __DIR__ . '/../../vendor/autoload.php';

use RodrigoPedra\RecordProcessor\ProcessorBuilder;

$processor = (new ProcessorBuilder)
    ->readFromCSVFile(__DIR__ . '/../../storage/input.csv')
    ->writeToHTMLTable()
    ->build();

$output = $processor->process();

echo $output->getOutput(), PHP_EOL;

exit;
