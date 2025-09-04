<?php

namespace RodrigoPedra\RecordProcessor\Serializers;

use RodrigoPedra\RecordProcessor\Concerns\CountsLines;
use RodrigoPedra\RecordProcessor\Concerns\HasPrefix;
use RodrigoPedra\RecordProcessor\Configurators\Serializers\EchoSerializerConfigurator;
use RodrigoPedra\RecordProcessor\Configurators\Serializers\SerializerConfigurator;
use RodrigoPedra\RecordProcessor\Contracts\Serializer;
use RodrigoPedra\RecordProcessor\RecordSerializers\TextRecordSerializer;
use RodrigoPedra\RecordProcessor\Support\FileInfo;

class EchoSerializer implements Serializer
{
    use CountsLines;
    use HasPrefix;

    protected readonly EchoSerializerConfigurator $configurator;

    protected ?\SplFileObject $writer = null;

    public function __construct()
    {
        $this->configurator = new EchoSerializerConfigurator($this, true, true);
    }

    public function open(): void
    {
        $this->lineCount = 0;
        $this->writer = FileInfo::createWritableFileObject(FileInfo::OUTPUT_STREAM);
    }

    public function close(): void
    {
        $this->writer = null;
    }

    public function append($content): void
    {
        if (\is_null($this->writer)) {
            $this->open();
        }

        $prefix = $this->prefix();

        if (\is_string($prefix)) {
            $this->writer->fwrite($prefix . ': ');
        }

        if (! \is_string($content)) {
            $content = \var_export($content, true);
        }

        $this->writer->fwrite($content);
        $this->writer->fwrite(\PHP_EOL);
        $this->writer->fwrite(\PHP_EOL);

        $this->incrementLineCount();
    }

    public function output(): null
    {
        return null;
    }

    public function configurator(): SerializerConfigurator
    {
        return $this->configurator;
    }

    public function defaultRecordSerializer(): TextRecordSerializer
    {
        return new TextRecordSerializer();
    }
}
