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

    protected ?\SplFileObject $output = null;

    public function __construct()
    {
        $this->configurator = new EchoSerializerConfigurator($this, true, true);
    }

    public function open(): void
    {
        $this->lineCount = 0;
        $this->output = FileInfo::createWritableFileObject(FileInfo::OUTPUT_STREAM);
    }

    public function close(): void
    {
        $this->output = null;
    }

    public function append($content): void
    {
        if (\is_null($this->output)) {
            $this->open();
        }

        $prefix = $this->prefix();

        if (\is_string($prefix)) {
            $this->output->fwrite($prefix . ': ');
        }

        if (! \is_string($content)) {
            $content = \var_export($content, true);
        }

        $this->output->fwrite($content);
        $this->output->fwrite(\PHP_EOL);
        $this->output->fwrite(\PHP_EOL);

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
