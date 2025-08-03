<?php

namespace RodrigoPedra\RecordProcessor\Serializers;

use RodrigoPedra\RecordProcessor\Concerns\HasPrefix;
use RodrigoPedra\RecordProcessor\Configurators\Serializers\EchoSerializerConfigurator;
use RodrigoPedra\RecordProcessor\RecordSerializers\TextRecordSerializer;
use RodrigoPedra\RecordProcessor\Support\FileInfo;

class EchoSerializer extends FileSerializer
{
    use HasPrefix;

    public function __construct()
    {
        parent::__construct(FileInfo::OUTPUT_STREAM);

        $this->configurator = new EchoSerializerConfigurator($this, true, true);
    }

    public function open()
    {
        $this->lineCount = 0;
    }

    public function append($content): void
    {
        $prefix = $this->prefix();

        if (\is_string($prefix)) {
            $this->file->fwrite($prefix . ': ');
        }

        if (! \is_string($content)) {
            $content = \var_export($content, true);
        }

        $this->file->fwrite($content);
        $this->file->fwrite(\PHP_EOL);
        $this->file->fwrite(\PHP_EOL);

        $this->incrementLineCount();
    }

    public function output(): ?\SplFileObject
    {
        return null;
    }

    public function defaultRecordSerializer(): TextRecordSerializer
    {
        return new TextRecordSerializer();
    }
}
