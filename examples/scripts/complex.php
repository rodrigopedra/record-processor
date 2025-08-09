#!/usr/bin/env php
<?php

require __DIR__ . '/../../vendor/autoload.php';

use RodrigoPedra\RecordProcessor\Configurators\Serializers\HTMLTableSerializerConfigurator;
use RodrigoPedra\RecordProcessor\Configurators\Serializers\SerializerAddonCallback;
use RodrigoPedra\RecordProcessor\Examples\RecordObjects\ExampleRecordSerializer;
use RodrigoPedra\RecordProcessor\ProcessorBuilder;

$storagePath = __DIR__ . '/../../storage/';

$processor = (new ProcessorBuilder())
    ->readFromExcelFile($storagePath . 'input.xlsx')
    ->serializeToExcelFile($storagePath . 'output.xlsx')
    ->serializeToHTMLTable(function (HTMLTableSerializerConfigurator $configurator) use ($storagePath): void {
        $configurator->withRecordSerializer(new ExampleRecordSerializer());

        $configurator->withHeader(['name', 'email']);

        $configurator->withTrailler(function (SerializerAddonCallback $serializer): void {
            $recordCount = $serializer->recordCount();
            $serializer->append($recordCount . ' records');
        });

        $configurator->withTableClassAttribute('table table-condensed');
        $configurator->withTableIdAttribute('my-table');

        $configurator->writeOutputToFile($storagePath . 'output.html');
    })
    ->aggregateRecordsByKey()
    ->serializeToCSVFile($storagePath . 'output.csv')
    ->build();

$output = $processor->process();

echo 'input lines: ', $output->inputLineCount(), \PHP_EOL;
echo 'input records: ', $output->inputRecordCount(), \PHP_EOL;
echo 'output lines: ', $output->outputLineCount(), \PHP_EOL;
echo 'output records: ', $output->outputRecordCount(), \PHP_EOL;
echo $output->output(), \PHP_EOL;
