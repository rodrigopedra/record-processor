<?php

namespace RodrigoPedra\RecordProcessor\Serializers;

use RodrigoPedra\RecordProcessor\Concerns\CountsLines;
use RodrigoPedra\RecordProcessor\Configurators\Serializers\SerializerConfigurator;
use RodrigoPedra\RecordProcessor\Contracts\RecordSerializer;
use RodrigoPedra\RecordProcessor\Contracts\Serializer;
use RodrigoPedra\RecordProcessor\RecordSerializers\ArrayRecordSerializer;
use RodrigoPedra\RecordProcessor\Support\FileInfo;
use RodrigoPedra\RecordProcessor\Support\PhpStream;

abstract class FileSerializer implements Serializer
{
    use CountsLines;

    protected readonly \SplFileObject $file;

    public function __construct(
        protected readonly SerializerConfigurator $configurator,
        \SplFileInfo|string|null $file = null,
    ) {
        $this->file = FileInfo::createWritableFileObject($file ?? PhpStream::TEMP);
    }

    public function open(): void
    {
        $this->lineCount = 0;
    }

    public function close(): void {}

    public function output(): string
    {
        if (PhpStream::isTempFile($this->file)) {
            return \file_get_contents($this->file->getRealPath());
        }

        if (PhpStream::isMemoryFile($this->file)) {
            $this->file->setFlags(0);
            $this->file->rewind();

            return \implode('', \iterator_to_array($this->file));
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
