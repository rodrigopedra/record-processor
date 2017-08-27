#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';

use RodrigoPedra\RecordProcessor\Examples\ExampleRecordAggregateFormatter;
use RodrigoPedra\RecordProcessor\Helpers\WriterConfigurator;
use RodrigoPedra\RecordProcessor\ProcessorBuilder;

$processor = ( new ProcessorBuilder )
    ->readFromExcel( __DIR__ . '/../storage/input.xlsx' )
    ->writeToHTMLTable( function ( WriterConfigurator $configurator ) {
        $configurator->setTableClassAttribute( 'table table-condensed' );
        $configurator->setTableIdAttribute( 'my-table' );
    } )
    ->writeToExcel( __DIR__ . '/../storage/output.xlsx' )
    ->aggregateRecordsByKey( new ExampleRecordAggregateFormatter( 1 ) )
    ->writeToCSV( __DIR__ . '/../storage/output.csv' )
    ->build();

$output = $processor->process();

echo 'input lines: ', $output->getInputLineCount(), PHP_EOL;
echo 'input records: ', $output->getInputRecordCount(), PHP_EOL;
echo 'output lines: ', $output->getOutputLineCount(), PHP_EOL;
echo 'output records: ', $output->getOutputRecordCount(), PHP_EOL;
echo $output->getOutput(), PHP_EOL;
