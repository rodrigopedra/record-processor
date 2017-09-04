#!/usr/bin/env php
<?php

require __DIR__ . '/../../vendor/autoload.php';

use RodrigoPedra\RecordProcessor\Examples\RecordObjects\ExampleRecordAggregateFormatter;
use RodrigoPedra\RecordProcessor\Examples\RecordObjects\ExampleRecordFormatter;
use RodrigoPedra\RecordProcessor\Helpers\LaravelExcel\Factory;
use RodrigoPedra\RecordProcessor\Helpers\WriterCallbackProxy;
use RodrigoPedra\RecordProcessor\Helpers\WriterConfigurator;
use RodrigoPedra\RecordProcessor\ProcessorBuilder;

$storagePath = __DIR__ . '/../../storage/';

$excel = Factory::getExcel();

$processor = ( new ProcessorBuilder )
    ->setExcel( $excel )
    ->readFromExcelFile( $storagePath . 'input.xlsx' )
    ->writeToExcelFile( $storagePath . 'output.xlsx' )
    ->writeToHTMLTable( function ( WriterConfigurator $configurator ) use ( $storagePath ) {
        $configurator->setHeader( [ 'name', 'email' ] );

        $configurator->setTrailler( function ( WriterCallbackProxy $writer ) use ( $storagePath ) {
            $recordCount = $writer->getRecordCount();
            $writer->append( $recordCount . ' records' );
        } );

        $configurator->setTableClassAttribute( 'table table-condensed' );
        $configurator->setTableIdAttribute( 'my-table' );

        $configurator->writeOutputToFile( $storagePath . 'output.html' );
    } )
    ->usingFormatter( new ExampleRecordFormatter )
    ->aggregateRecordsByKey( new ExampleRecordAggregateFormatter )
    ->writeToCSVFile( $storagePath . 'output.csv' )
    ->build();

$output = $processor->process();

echo 'input lines: ', $output->getInputLineCount(), PHP_EOL;
echo 'input records: ', $output->getInputRecordCount(), PHP_EOL;
echo 'output lines: ', $output->getOutputLineCount(), PHP_EOL;
echo 'output records: ', $output->getOutputRecordCount(), PHP_EOL;
echo $output->getOutput(), PHP_EOL;
