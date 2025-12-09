<?php

namespace RodrigoPedra\RecordProcessor\Serializers;

use Illuminate\Support\Arr;
use League\Csv\Bom;
use League\Csv\Writer;
use RodrigoPedra\RecordProcessor\Concerns\HasCSVControls;
use RodrigoPedra\RecordProcessor\Configurators\Serializers\CSVFileSerializerConfigurator;
use RodrigoPedra\RecordProcessor\Support\EOL;
use RodrigoPedra\RecordProcessor\Support\FileInfo;

/**
 * @property \RodrigoPedra\RecordProcessor\Configurators\Serializers\CSVFileSerializerConfigurator $configurator
 */
class CSVFileSerializer extends FileSerializer
{
    use HasCSVControls;

    protected ?Writer $writer = null;

    public function __construct(\SplFileInfo|string|null $file = null)
    {
        parent::__construct(
            configurator: new CSVFileSerializerConfigurator($this, true, true),
            file: $file,
        );

        $this->withOutputBOM(Bom::Utf8);
        $this->withDelimiter(';');
        $this->withEndOfLine(EOL::WINDOWS);
    }

    /**
     * @throws \League\Csv\InvalidArgument
     * @throws \League\Csv\UnavailableStream
     */
    public function open(): void
    {
        parent::open();

        $file = FileInfo::createWritableFileObject($this->file);

        $this->writer = Writer::from($file);
        $this->writer->setOutputBOM($this->outputBOM());
        $this->writer->setDelimiter($this->delimiter());
        $this->writer->setEnclosure($this->enclosure());
        $this->writer->setEndOfLine($this->endOfLine());
        $this->writer->setEscape($this->escape());
    }

    public function close(): void
    {
        $this->writer = null;
    }

    /**
     * @throws \League\Csv\CannotInsertRecord
     * @throws \League\Csv\Exception
     */
    public function append($content): void
    {
        $this->writer->insertOne(Arr::wrap($content));

        $this->incrementLineCount();
    }
}
