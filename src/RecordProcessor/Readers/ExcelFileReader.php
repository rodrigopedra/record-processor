<?php

namespace RodrigoPedra\RecordProcessor\Readers;

use RodrigoPedra\RecordProcessor\Contracts\ConfigurableReader;
use RodrigoPedra\RecordProcessor\Traits\ConfiguresExcelReader;

class ExcelFileReader extends FileReader implements ConfigurableReader
{
    use ConfiguresExcelReader;

    public function open()
    {
        parent::open();

        $excel = app( 'excel' );

        /** @var  \Maatwebsite\Excel\Readers\LaravelExcelReader $reader */
        $configuratorCallback = $this->getReaderConfigurator();

        $reader = $excel->load( $this->file->getRealPath(), $this->getReaderConfigurator() );

        if (is_null( $configuratorCallback )) {
            $reader->setSelectedSheetIndices( 0 );
        }

        /** @var  \Maatwebsite\Excel\Collections\ExcelCollection $collection */
        $collection = $reader->get();
        $this->setInnerIterator( $collection->getIterator() );
    }

    public function current()
    {
        $cellCollection = $this->iteratorCurrent();

        return $cellCollection->toArray();
    }
}
