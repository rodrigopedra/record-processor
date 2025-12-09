<?php

namespace RodrigoPedra\RecordProcessor\Serializers;

use Illuminate\Support\Arr;
use League\Csv\HTMLConverter;
use RodrigoPedra\RecordProcessor\Configurators\Serializers\HTMLTableSerializerConfigurator;
use RodrigoPedra\RecordProcessor\Contracts\Serializer;
use RodrigoPedra\RecordProcessor\RecordSerializers\ArrayRecordSerializer;
use RodrigoPedra\RecordProcessor\Support\EOL;
use RodrigoPedra\RecordProcessor\Support\FileInfo;

class HTMLTableSerializer implements Serializer
{
    protected readonly HTMLTableSerializerConfigurator $configurator;

    protected string $tableClassAttribute = '';

    protected string $tableIdAttribute = '';

    protected ?FileInfo $file = null;

    protected ?\SplFileObject $writer = null;

    protected \SplFileInfo|string|null $output = null;

    protected ?array $records = null;

    public function __construct()
    {
        $this->configurator = new HTMLTableSerializerConfigurator($this, true, true);
    }

    public function writeOutputToFile(string $fileName): static
    {
        $this->file = new FileInfo($fileName);

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
        $this->output = null;

        if ($this->file) {
            $this->writer = FileInfo::createWritableFileObject($this->file);
        }
    }

    /**
     * @throws \DOMException
     */
    public function close(): void
    {
        if ($this->writer) {
            $this->output = $this->writer->getFileInfo(FileInfo::class);

            $this->writer->fwrite($this->convert());
            $this->writer->fwrite(EOL::UNIX->value);
            $this->writer = null;
        } else {
            $this->output = $this->convert();
        }

        $this->records = null;
    }

    public function append($content): void
    {
        if (\is_null($this->records)) {
            $this->open();
        }

        $this->records[] = \array_map(\strval(...), Arr::wrap($content));
    }

    public function lineCount(): int
    {
        return \count($this->records ?? []);
    }

    /**
     * @throws \DOMException
     */
    public function convert(): string
    {
        $records = $this->records ?? [];

        $header = \is_null($this->configurator->header())
            ? []
            : \array_shift($records);

        $footer = \is_null($this->configurator->trailler())
            ? []
            : \array_pop($records);

        // should be chained, ->table() returns a cloned HTMLConverter instance
        return (new HTMLConverter())
            ->table($this->tableClassAttribute, $this->tableIdAttribute)
            ->convert($records, $header ?? [], $footer ?? []);
    }

    public function output(): ?string
    {
        if ($this->output instanceof \SplFileInfo) {
            return $this->output->getRealPath();
        }

        return $this->output;
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
