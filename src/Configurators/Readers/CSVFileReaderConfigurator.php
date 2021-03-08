<?php

namespace RodrigoPedra\RecordProcessor\Configurators\Readers;

use RodrigoPedra\RecordProcessor\Reader\CSVFileReader;

/**
 * @property  \RodrigoPedra\RecordProcessor\Reader\CSVFileReader $reader
 */
class CSVFileReaderConfigurator extends ReaderConfigurator
{
    public function __construct(CSVFileReader $reader)
    {
        parent::__construct($reader);
    }

    public function withDelimiter(string $delimiter): self
    {
        $this->reader->withDelimiter($delimiter);

        return $this;
    }

    public function withEnclosure(string $enclosure): self
    {
        $this->reader->withEnclosure($enclosure);

        return $this;
    }

    public function withEscape(string $escape): self
    {
        $this->reader->withEscape($escape);

        return $this;
    }

    public function useFirstRowAsHeader(bool $firstRowAsHeader = true): self
    {
        $this->reader->useFirstRowAsHeader($firstRowAsHeader);

        return $this;
    }
}
