<?php

require __DIR__ . '/../../vendor/autoload.php';

use RodrigoPedra\RecordProcessor\Configurators\Serializers\SerializerConfigurator;
use RodrigoPedra\RecordProcessor\ProcessorBuilder;

$storagePath = __DIR__ . '/../../storage/';

$processor = (new ProcessorBuilder())
    ->readFromCSVFile($storagePath . 'input.csv')
    ->serializeToExcelFile($storagePath . 'output.xlsx', function (SerializerConfigurator $configurator): void {
        $configurator->withHeader(['name', 'email']);
    })
    ->downloadFileOutput('report.xlsx', deleteFileAfterDownload: true)
    ->build();

$processor->process();

exit;
