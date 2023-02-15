<?php

namespace RodrigoPedra\RecordProcessor\Serializers;

use Illuminate\Support\Arr;
use League\Csv\HTMLConverter;
use RodrigoPedra\RecordProcessor\Concerns\CountsLines;
use RodrigoPedra\RecordProcessor\Configurators\Serializers\HTMLTableSerializerConfigurator;
use RodrigoPedra\RecordProcessor\Contracts\Serializer;
use RodrigoPedra\RecordProcessor\RecordSerializers\ArrayRecordSerializer;
use RodrigoPedra\RecordProcessor\Support\FileInfo;
use RodrigoPedra\RecordProcessor\Support\NewLines;

class HTMLTableSerializer implements Serializer
{
    use CountsLines;

    protected ?HTMLConverter $converter = null;
    protected array $records = [];
    protected string $tableClassAttribute = '';
    protected string $tableIdAttribute = '';
    protected ?\SplFileObject $file = null;
    protected \SplFileObject|string $output = '';
    protected HTMLTableSerializerConfigurator $configurator;

    public function __construct()
    {
        $this->configurator = new HTMLTableSerializerConfigurator($this, true, true);
    }

    public function writeOutputToFile(string $fileName): static
    {
        $this->file = FileInfo::createWritableFileObject($fileName);

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

    /**
     * @throws \DOMException
     */
    public function open()
    {
        $this->lineCount = 0;
        $this->output = '';
        $this->records = [];

        // should be chained, ->table() returns a cloned HTMLConverter instance
        $this->converter = (new HTMLConverter())
            ->table($this->tableClassAttribute, $this->tableIdAttribute);
    }

    public function close()
    {
        $this->output = $this->converter->convert($this->records);

        if (! \is_null($this->file)) {
            $this->file->fwrite($this->output);
            $this->file->fwrite(NewLines::UNIX_NEWLINE);

            $this->output = FileInfo::createReadableFileObject($this->file);
        }

        $this->converter = null;
        $this->records = [];
    }

    public function append($content)
    {
        $this->records[] = Arr::wrap($content);

        $this->incrementLineCount();
    }

    public function output(): \SplFileObject|string
    {
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
