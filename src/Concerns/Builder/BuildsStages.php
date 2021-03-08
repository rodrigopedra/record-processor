<?php

namespace RodrigoPedra\RecordProcessor\Concerns\Builder;

use Psr\Log\LogLevel;
use RodrigoPedra\RecordProcessor\Contracts\RecordAggregateFactory;
use RodrigoPedra\RecordProcessor\RecordSerializers\ArrayRecordSerializer;
use RodrigoPedra\RecordProcessor\Stages\DownloadFileOutput;
use RodrigoPedra\RecordProcessor\Stages\RecordAggregator;
use RodrigoPedra\RecordProcessor\Stages\Writer;
use RodrigoPedra\RecordProcessor\Stages\ValidRecords;
use RodrigoPedra\RecordProcessor\Serializers\EchoSerializer;
use RodrigoPedra\RecordProcessor\Serializers\LogSerializer;

trait BuildsStages
{
    public function aggregateRecordsByKey(?RecordAggregateFactory $recordAggregateFactory = null): self
    {
        $this->addStage(new RecordAggregator($recordAggregateFactory));

        return $this;
    }

    public function onlyValidRecords(): self
    {
        $this->addStage(new ValidRecords());

        return $this;
    }

    public function downloadFileOutput($outputFilename = '', $deleteFileAfterDownload = false): self
    {
        $this->addStage(new DownloadFileOutput($outputFilename, $deleteFileAfterDownload));

        return $this;
    }

    public function logRecords($prefix = null): self
    {
        $serializer = new LogSerializer($this->logger());
        $serializer->withLevel(LogLevel::DEBUG);
        $serializer->withPrefix($prefix);
        $serializer->configurator()->withRecordSerializer(new ArrayRecordSerializer());

        $writer = new Writer($serializer);
        $this->addStage($writer);

        return $this;
    }

    public function logInvalidRecords($prefix = 'INVALID'): self
    {
        $serializer = new LogSerializer($this->logger());
        $serializer->withLevel(LogLevel::ERROR);
        $serializer->withPrefix($prefix);
        $serializer->configurator()->withRecordSerializer(new ArrayRecordSerializer());

        $writer = new Writer($serializer);
        $this->addStage($writer);

        return $this;
    }

    public function echoRecords($prefix = null): self
    {
        $serializer = new EchoSerializer();
        $serializer->withPrefix($prefix);
        $serializer->configurator()->withRecordSerializer(new ArrayRecordSerializer());

        $writer = new Writer($serializer);
        $this->addStage($writer);

        return $this;
    }
}
