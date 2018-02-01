<?php

require __DIR__ . '/../../vendor/autoload.php';

use RodrigoPedra\RecordProcessor\Helpers\Writers\WriterConfigurator;
use RodrigoPedra\RecordProcessor\ProcessorBuilder;
use RodrigoPedra\RecordProcessor\Stages\DownloadFileOutput;

$storagePath = __DIR__ . '/../../storage/';

$processor = ( new ProcessorBuilder )
    ->readFromCSVFile( $storagePath . 'input.csv' )
    ->writeToExcelFile( $storagePath . 'output.xlsx', function ( WriterConfigurator $configurator ) {
        $configurator->setHeader( [ 'name', 'email' ] );
    } )
    ->downloadFileOutput( 'report.xlsx', DownloadFileOutput::DELETE_FILE_AFTER_DOWNLOAD )
    ->build();

$processor->process();

exit;
