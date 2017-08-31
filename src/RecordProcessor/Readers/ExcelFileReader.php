<?php

namespace RodrigoPedra\RecordProcessor\Readers;

use Maatwebsite\Excel\Excel;
use RodrigoPedra\RecordProcessor\Contracts\ConfigurableReader;
use RodrigoPedra\RecordProcessor\Traits\ConfiguresExcelReader;

class ExcelFileReader extends FileReader implements ConfigurableReader
{
    use ConfiguresExcelReader;

    /** @var Excel */
    protected $excel;

    public function __construct( $file, Excel $excel )
    {
        parent::__construct( $file );

        $this->excel = $excel;
    }

    public function open()
    {
        parent::open();

        /** @var  \Maatwebsite\Excel\Readers\LaravelExcelReader $reader */
        $configuratorCallback = $this->getReaderConfigurator();

        $reader = $this->excel->load( $this->file->getRealPath(), $this->getReaderConfigurator() );

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
