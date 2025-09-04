<?php

namespace RodrigoPedra\RecordProcessor\Serializers;

use RodrigoPedra\RecordProcessor\Configurators\Serializers\TextFileSerializerConfigurator;
use RodrigoPedra\RecordProcessor\RecordSerializers\TextRecordSerializer;
use RodrigoPedra\RecordProcessor\Support\FileInfo;
use RodrigoPedra\RecordProcessor\Support\NewLines;

/**
 * @property \RodrigoPedra\RecordProcessor\Configurators\Serializers\TextFileSerializerConfigurator $configurator
 */
class TextFileSerializer extends FileSerializer
{
    protected string $newLine = NewLines::WINDOWS_NEWLINE;

    protected ?\SplFileObject $writer = null;

    public function __construct(\SplFileObject|string|null $file = null)
    {
        parent::__construct(
            configurator: new TextFileSerializerConfigurator($this, true, true),
            file: FileInfo::createWritableFileObject($file),
        );
    }

    public function newLine(): string
    {
        return $this->newLine;
    }

    public function withNewLine(string $newLine): static
    {
        $this->newLine = $newLine;

        return $this;
    }

    public function open(): void
    {
        $this->lineCount = 0;
        $this->writer = FileInfo::createWritableFileObject($this->file);
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

        if (! \is_string($content)) {
            throw new \InvalidArgumentException('Content for TextFileSerializer should be a string');
        }

        $content = \sprintf('%s%s', $content, $this->newLine());
        $this->writer->fwrite($content);

        $this->incrementLineCount(\substr_count($content, $this->newLine()));
    }

    public function defaultRecordSerializer(): TextRecordSerializer
    {
        return new TextRecordSerializer();
    }
}
