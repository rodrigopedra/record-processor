<?php

require __DIR__ . '/../../vendor/autoload.php';

use RodrigoPedra\RecordProcessor\Configurators\Serializers\SerializerConfigurator;
use RodrigoPedra\RecordProcessor\ProcessorBuilder;
use RodrigoPedra\RecordProcessor\Stages\DownloadFileOutput;

$storagePath = __DIR__ . '/../../storage/';

$processor = (new ProcessorBuilder())
    ->readFromCSVFile($storagePath . 'input.csv')
    ->serializeToExcelFile($storagePath . 'output.xlsx', function (SerializerConfigurator $configurator) {
        $configurator->withHeader(['name', 'email']);
    })
    ->downloadFileOutput('report.xlsx', DownloadFileOutput::DELETE_FILE_AFTER_DOWNLOAD)
    ->build();

$processor->process();

exit;
