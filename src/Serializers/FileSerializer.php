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
        \SplFileInfo|string|null $file,
    ) {
        $this->file = match (true) {
            $file instanceof \SplFileInfo => $file->getFileInfo(FileInfo::class),
            \is_string($file) => new FileInfo($file),
            default => FileInfo::createTempFileObject(),
        };
    }

    public function open(): void
    {
        $this->lineCount = 0;
    }

    public function close(): void {}

    public function output(): string
    {
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
