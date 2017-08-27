<?php

namespace RodrigoPedra\RecordProcessor\Readers;

use RodrigoPedra\RecordProcessor\Contracts\ConfigurableReader;
use RodrigoPedra\RecordProcessor\Traits\ConfiguresExcelReader;
use RodrigoPedra\RecordProcessor\Traits\ReaderInnerIterator;

class ExcelFileReader extends FileReader implements ConfigurableReader
{
    use ConfiguresExcelReader, ReaderInnerIterator {
        current as iteratorCurrent;
    }

    public function open()
    {
        $this->lineCount = 0;

        $excel = app( 'excel' );

        /** @var  \Maatwebsite\Excel\Readers\LaravelExcelReader $reader */
        $configuratorCallback = $this->getReaderConfigurator();

        $reader = $excel->load( $this->getRealPath(), $this->getReaderConfigurator() );

        if (is_null( $configuratorCallback )) {
            $reader->setSelectedSheetIndices( 0 );
        }

        /** @var  \Maatwebsite\Excel\Collections\ExcelCollection $collection */
        $collection = $reader->get();
        $this->setInnerIterator( $collection->getIterator() );
    }

    public function close()
    {
        $this->setInnerIterator( null );
    }

    public function current()
    {
        $cellCollection = $this->iteratorCurrent();

        return $cellCollection->toArray();
    }
}
