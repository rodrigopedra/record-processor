<?php

namespace RodrigoPedra\RecordProcessor\Serializers;

use Illuminate\Support\Arr;
use League\Csv\ByteSequence;
use League\Csv\Writer;
use RodrigoPedra\RecordProcessor\Concerns\HasCSVControls;
use RodrigoPedra\RecordProcessor\Configurators\Serializers\CSVFileSerializerConfigurator;
use RodrigoPedra\RecordProcessor\Support\NewLines;

class CSVFileSerializer extends FileSerializer implements ByteSequence
{
    use HasCSVControls;

    protected ?Writer $writer = null;

    public function __construct(\SplFileObject|string|null $file = null)
    {
        parent::__construct($file);

        $this->withDelimiter(';');
        $this->withNewline(NewLines::WINDOWS_NEWLINE);
        $this->withOutputBOM(static::BOM_UTF8);

        $this->configurator = new CSVFileSerializerConfigurator($this, true, true);
    }

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

    public function append($content)
    {
        $this->writer->insertOne(Arr::wrap($content));

        $this->incrementLineCount();
    }
}
