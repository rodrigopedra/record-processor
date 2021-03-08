<?php

namespace RodrigoPedra\RecordProcessor\Serializers;

use RodrigoPedra\RecordProcessor\Concerns\HasPrefix;
use RodrigoPedra\RecordProcessor\Concerns\NoOutput;
use RodrigoPedra\RecordProcessor\Configurators\Serializers\EchoSerializerConfigurator;
use RodrigoPedra\RecordProcessor\RecordSerializers\TextRecordSerializer;

class EchoSerializer extends FileSerializer
{
    use HasPrefix;
    use NoOutput;

    public function __construct()
    {
        parent::__construct('php://output');

        $this->configurator = new EchoSerializerConfigurator($this, true, true);
    }

    public function open()
    {
        $this->lineCount = 0;
    }

    public function append($content)
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

    public function defaultRecordSerializer(): TextRecordSerializer
    {
        return new TextRecordSerializer();
    }
}
