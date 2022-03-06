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

    public function withDelimiter(string $delimiter): static
    {
        $this->reader->withDelimiter($delimiter);

        return $this;
    }

    public function withEnclosure(string $enclosure): static
    {
        $this->reader->withEnclosure($enclosure);

        return $this;
    }

    public function withEscape(string $escape): static
    {
        $this->reader->withEscape($escape);

        return $this;
    }

    public function useFirstRowAsHeader(bool $firstRowAsHeader = true): static
    {
        $this->reader->useFirstRowAsHeader($firstRowAsHeader);

        return $this;
    }
}
