<?php

namespace RodrigoPedra\RecordProcessor\Reader;

use League\Csv\Reader as CsvReader;
use RodrigoPedra\RecordProcessor\Concerns\HasCSVControls;
use RodrigoPedra\RecordProcessor\Configurators\Readers\CSVFileReaderConfigurator;
use RodrigoPedra\RecordProcessor\Contracts\Reader;
use RodrigoPedra\RecordProcessor\Contracts\RecordParser;
use RodrigoPedra\RecordProcessor\RecordParsers\ArrayRecordParser;

class CSVFileReader extends FileReader implements Reader
{
    use HasCSVControls;

    protected CSVFileReaderConfigurator $configurator;
    protected bool $useFirstRowAsHeader = true;

    public function __construct($file)
    {
        parent::__construct($file);

        $this->configurator = new CSVFileReaderConfigurator($this);

        $this->withDelimiter(';');
        $this->withEnclosure('"');
        $this->withEscape('\\');
        $this->useFirstRowAsHeader();
    }

    public function useFirstRowAsHeader(bool $firstRowAsHeader = true)
    {
        $this->useFirstRowAsHeader = $firstRowAsHeader;
    }

    public function open()
    {
        parent::open();

        $csvReader = CsvReader::createFromFileObject($this->file);

        $csvReader->setDelimiter($this->delimiter());
        $csvReader->setEnclosure($this->enclosure());
        $csvReader->setEscape($this->escape());

        if ($this->useFirstRowAsHeader) {
            $csvReader->setHeaderOffset(0);
        }

        $this->withInnerIterator($csvReader->getRecords());
    }

    public function configurator(): CSVFileReaderConfigurator
    {
        return $this->configurator;
    }

    public function defaultRecordParser(): RecordParser
    {
        return new ArrayRecordParser();
    }
}
