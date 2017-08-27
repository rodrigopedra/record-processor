<?php

namespace RodrigoPedra\RecordProcessor\Readers;

use RodrigoPedra\RecordProcessor\Contracts\Reader;
use RodrigoPedra\RecordProcessor\Traits\CountsLines;
use RodrigoPedra\RecordProcessor\Traits\ReaderInnerIterator;

class ExcelReader implements Reader
{
    use CountsLines, ReaderInnerIterator {
        current as iteratorCurrent;
    }

    /** @var string */
    protected $filepath = '';

    public function __construct( $filepath )
    {
        $this->filepath = $filepath;
    }

    public function open()
    {
        $this->lineCount = 0;

        $excel = app( 'excel' );

        /** @var  \Maatwebsite\Excel\Readers\LaravelExcelReader $reader */
        $reader = $excel->load( $this->filepath );
        $reader->setSelectedSheetIndices( 0 );
        $reader->noHeading();

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
