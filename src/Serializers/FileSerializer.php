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

    protected ?\SplFileObject $file;
    protected FileInfo $fileInfo;
    protected SerializerConfigurator $configurator;

    public function __construct(\SplFileObject|string|null $file)
    {
        $file ??= FileInfo::TEMP_FILE;

        $this->file = FileInfo::createWritableFileObject($file);
        $this->fileInfo = $this->file->getFileInfo(FileInfo::class);
    }

    public function open()
    {
        $this->lineCount = 0;
        $this->file->ftruncate(0);
    }

    public function close()
    {
    }

    public function output(): ?\SplFileObject
    {
        return FileInfo::createReadableFileObject($this->file);
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
