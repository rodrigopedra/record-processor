<?php

namespace RodrigoPedra\RecordProcessor\Serializers;

use RodrigoPedra\RecordProcessor\Concerns\CountsLines;
use RodrigoPedra\RecordProcessor\Configurators\Serializers\SerializerConfigurator;
use RodrigoPedra\RecordProcessor\Contracts\RecordSerializer;
use RodrigoPedra\RecordProcessor\Contracts\Serializer;
use RodrigoPedra\RecordProcessor\RecordSerializers\ArrayRecordSerializer;
use RodrigoPedra\RecordProcessor\Support\FileInfo;

abstract class FileSerializer implements Serializer
{
    use CountsLines;

    protected readonly FileInfo $file;

    public function __construct(
        protected readonly SerializerConfigurator $configurator,
        \SplFileInfo|string|null $file = null,
    ) {
        $this->file = FileInfo::createWritableFileObject($file ?? FileInfo::TEMP_FILE)->getFileInfo(FileInfo::class);
    }

    public function open(): void
    {
        $this->lineCount = 0;
    }

    public function close(): void {}

    public function output(): string
    {
        if ($this->file->isTempFile()) {
            return \file_get_contents($this->file->getRealPath());
        }

        return $this->file->getRealPath();
    }

    public function configurator(): SerializerConfigurator
    {
        return $this->configurator;
    }

    public function defaultRecordSerializer(): RecordSerializer
    {
        return new ArrayRecordSerializer();
    }
}
