<?php

namespace RodrigoPedra\RecordProcessor\Concerns\Builder;

use Psr\Log\LogLevel;
use RodrigoPedra\RecordProcessor\Contracts\RecordAggregateFactory;
use RodrigoPedra\RecordProcessor\RecordSerializers\ArrayRecordSerializer;
use RodrigoPedra\RecordProcessor\Serializers\EchoSerializer;
use RodrigoPedra\RecordProcessor\Serializers\LogSerializer;
use RodrigoPedra\RecordProcessor\Stages\DownloadFileOutput;
use RodrigoPedra\RecordProcessor\Stages\RecordAggregator;
use RodrigoPedra\RecordProcessor\Stages\ValidRecords;
use RodrigoPedra\RecordProcessor\Stages\Writer;

trait BuildsStages
{
    public function aggregateRecordsByKey(?RecordAggregateFactory $recordAggregateFactory = null): static
    {
        $this->addStage(new RecordAggregator($recordAggregateFactory));

        return $this;
    }

    public function onlyValidRecords(): static
    {
        $this->addStage(new ValidRecords());

        return $this;
    }

    public function downloadFileOutput(string $outputFilename = '', bool $deleteFileAfterDownload = false): static
    {
        $this->addStage(new DownloadFileOutput($outputFilename, $deleteFileAfterDownload));

        return $this;
    }

    public function logRecords(?string $prefix = null): static
    {
        $serializer = new LogSerializer($this);
        $serializer->withLevel(LogLevel::DEBUG);
        $serializer->withPrefix($prefix);
        $serializer->configurator()->withRecordSerializer(new ArrayRecordSerializer());

        $writer = new Writer($serializer);
        $this->addStage($writer);

        return $this;
    }

    public function logInvalidRecords(string $prefix = 'INVALID'): static
    {
        $serializer = new LogSerializer($this);
        $serializer->withLevel(LogLevel::ERROR);
        $serializer->withPrefix($prefix);
        $serializer->configurator()->withRecordSerializer(new ArrayRecordSerializer());

        $writer = new Writer($serializer);
        $this->addStage($writer);

        return $this;
    }

    public function echoRecords(?string $prefix = null): static
    {
        $serializer = new EchoSerializer();
        $serializer->withPrefix($prefix);
        $serializer->configurator()->withRecordSerializer(new ArrayRecordSerializer());

        $writer = new Writer($serializer);
        $this->addStage($writer);

        return $this;
    }
}
