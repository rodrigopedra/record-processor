<?php

namespace RodrigoPedra\RecordProcessor\Configurators\Readers;

use RodrigoPedra\RecordProcessor\Reader\ExcelFileReader;

/**
 * @property  \RodrigoPedra\RecordProcessor\Reader\ExcelFileReader $reader
 */
class ExcelFileReaderConfigurator extends ReaderConfigurator
{
    public function __construct(ExcelFileReader $reader)
    {
        parent::__construct($reader);
    }

    public function noHeading(bool $skipHeading = true): self
    {
        $this->reader->noHeading($skipHeading);

        return $this;
    }

    public function withSelectedSheetIndex(int $index): self
    {
        $this->reader->withSelectedSheetIndex($index);

        return $this;
    }
}
