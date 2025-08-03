<?php

namespace RodrigoPedra\RecordProcessor\Serializers;

use Illuminate\Support\Arr;
use League\Csv\Bom;
use League\Csv\Writer;
use RodrigoPedra\RecordProcessor\Concerns\HasCSVControls;
use RodrigoPedra\RecordProcessor\Configurators\Serializers\CSVFileSerializerConfigurator;
use RodrigoPedra\RecordProcessor\Support\NewLines;

class CSVFileSerializer extends FileSerializer
{
    use HasCSVControls;

    protected ?Writer $writer = null;

    public function __construct(\SplFileObject|string|null $file = null)
    {
        parent::__construct($file);

        $this->outputBOM = Bom::Utf8; // Initialize before using trait methods
        $this->withDelimiter(';');
        $this->withNewline(NewLines::WINDOWS_NEWLINE);
        $this->withOutputBOM(Bom::Utf8);

        $this->configurator = new CSVFileSerializerConfigurator($this, true, true);
    }

    /**
     * @throws \League\Csv\InvalidArgument
     */
    public function open()
    {
        parent::open();

        $this->writer = Writer::createFromFileObject($this->file);
        $this->writer->setOutputBOM($this->outputBOM());
        $this->writer->setDelimiter($this->delimiter());
        $this->writer->setEnclosure($this->enclosure());
        $this->writer->setNewline($this->newline());
        $this->writer->setEscape($this->escape());
    }

    public function close()
    {
        $this->writer = null;
    }

    /**
     * @throws \League\Csv\CannotInsertRecord
     */
    public function append($content): void
    {
        $this->writer->insertOne(Arr::wrap($content));

        $this->incrementLineCount();
    }
}
