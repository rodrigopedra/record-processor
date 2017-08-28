<?php

require __DIR__ . '/../../vendor/autoload.php';

use RodrigoPedra\RecordProcessor\ProcessorBuilder;

$storagePath = __DIR__ . '/../../storage/';

$processor = ( new ProcessorBuilder )
    ->readFromCSVFile( $storagePath . 'input.csv' )
    ->writeToExcelFile( $storagePath . 'input.xlsx' )
    ->downloadFileOutput( 'report.xlsx' )
    ->build();

$processor->process();

exit;
