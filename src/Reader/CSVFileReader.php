<?php

namespace RodrigoPedra\RecordProcessor\Reader;

use League\Csv\Reader as CsvReader;
use RodrigoPedra\RecordProcessor\Concerns\HasCSVControls;
use RodrigoPedra\RecordProcessor\Configurators\Readers\CSVFileReaderConfigurator;

/**
 * @property \RodrigoPedra\RecordProcessor\Configurators\Readers\CSVFileReaderConfigurator $configurator
 */
class CSVFileReader extends FileReader
{
    use HasCSVControls;

    protected bool $useFirstRowAsHeader = true;

    public function __construct(\SplFileInfo|string $file)
    {
        parent::__construct(
            configurator: new CSVFileReaderConfigurator($this),
            file: $file,
        );

        $this->withDelimiter(';');
        $this->withEnclosure('"');
        $this->withEscape('\\');
        $this->useFirstRowAsHeader();
    }

    public function useFirstRowAsHeader(bool $firstRowAsHeader = true): void
    {
        $this->useFirstRowAsHeader = $firstRowAsHeader;
    }

    /**
     * @throws \League\Csv\InvalidArgument
     * @throws \League\Csv\Exception
     */
    public function open(): void
    {
        parent::open();

        $csvReader = CsvReader::from($this->file);

        $csvReader->setDelimiter($this->delimiter());
        $csvReader->setEnclosure($this->enclosure());
        $csvReader->setEscape($this->escape());

        if ($this->useFirstRowAsHeader) {
            $csvReader->setHeaderOffset(0);
        }

        $this->withInnerIterator($csvReader->getRecords());
    }
}
