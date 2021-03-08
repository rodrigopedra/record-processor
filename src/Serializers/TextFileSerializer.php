<?php

namespace RodrigoPedra\RecordProcessor\Serializers;

use RodrigoPedra\RecordProcessor\Configurators\Serializers\TextFileSerializerConfigurator;
use RodrigoPedra\RecordProcessor\RecordSerializers\TextRecordSerializer;
use RodrigoPedra\RecordProcessor\Support\NewLines;

class TextFileSerializer extends FileSerializer
{
    protected string $newLine = NewLines::WINDOWS_NEWLINE;

    public function __construct($file = null)
    {
        parent::__construct($file);

        $this->configurator = new TextFileSerializerConfigurator($this, true, true);
    }

    public function newLine(): string
    {
        return $this->newLine;
    }

    public function withNewLine(string $newLine): self
    {
        $this->newLine = $newLine;

        return $this;
    }

    public function append($content)
    {
        if (! \is_string($content)) {
            throw new \InvalidArgumentException('Content for TextFileSerializer should be a string');
        }

        $content = \sprintf('%s%s', $content, $this->newLine());
        $this->file->fwrite($content);

        $this->incrementLineCount(\substr_count($content, $this->newLine()));
    }

    public function defaultRecordSerializer(): TextRecordSerializer
    {
        return new TextRecordSerializer();
    }
}
