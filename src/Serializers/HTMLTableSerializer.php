<?php

namespace RodrigoPedra\RecordProcessor\Serializers;

use Illuminate\Support\Arr;
use League\Csv\HTMLConverter;
use RodrigoPedra\RecordProcessor\Configurators\Serializers\HTMLTableSerializerConfigurator;
use RodrigoPedra\RecordProcessor\Contracts\Serializer;
use RodrigoPedra\RecordProcessor\RecordSerializers\ArrayRecordSerializer;
use RodrigoPedra\RecordProcessor\Support\FileInfo;
use RodrigoPedra\RecordProcessor\Support\NewLines;

class HTMLTableSerializer implements Serializer
{
    protected readonly HTMLTableSerializerConfigurator $configurator;

    protected string $tableClassAttribute = '';

    protected string $tableIdAttribute = '';

    protected ?string $filename = null;

    protected ?array $records = null;

    public function __construct()
    {
        $this->configurator = new HTMLTableSerializerConfigurator($this, true, true);
    }

    public function writeOutputToFile(string $fileName): static
    {
        $this->filename = $fileName;

        return $this;
    }

    public function withTableClassAttribute(string $tableClassAttribute): static
    {
        $this->tableClassAttribute = $tableClassAttribute;

        return $this;
    }

    public function withTableIdAttribute(string $tableIdAttribute): static
    {
        $this->tableIdAttribute = $tableIdAttribute;

        return $this;
    }

    public function open(): void
    {
        $this->records = [];
    }

    public function close(): void
    {
        if (! \is_null($this->filename)) {
            $file = FileInfo::createWritableFileObject($this->filename);
            $file->fwrite($this->output());
            $file->fwrite(NewLines::UNIX_NEWLINE);
        }

        $this->records = null;
    }

    public function append($content): void
    {
        if (\is_null($this->records)) {
            $this->open();
        }

        $this->records[] = Arr::wrap($content);
    }

    public function lineCount(): int
    {
        return \count($this->records ?? []);
    }

    public function output(): string
    {
        // should be chained, ->table() returns a cloned HTMLConverter instance
        return (new HTMLConverter())
            ->table($this->tableClassAttribute, $this->tableIdAttribute)
            ->convert($this->records ?? []);
    }

    public function configurator(): HTMLTableSerializerConfigurator
    {
        return $this->configurator;
    }

    public function defaultRecordSerializer(): ArrayRecordSerializer
    {
        return new ArrayRecordSerializer();
    }
}
