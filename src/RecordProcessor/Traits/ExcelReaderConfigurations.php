<?php

namespace RodrigoPedra\RecordProcessor\Traits;

trait ExcelReaderConfigurations
{
    protected $skipRows           = 0;
    protected $selectedSheetIndex = 0;

    public function setSkipRows( $rows )
    {
        $this->skipRows = $rows;
    }

    public function getSkipRows()
    {
        return $this->skipRows;
    }

    public function setSelectedSheetIndex( $index )
    {
        $this->selectedSheetIndex = $index;
    }

    public function getSelectedSheetIndex()
    {
        return $this->selectedSheetIndex;
    }

    // compatibility with Maatwebsite\Excel (Laravel Excel)
    public function setSelectedSheetIndices( $index = 0 )
    {
        $this->setSelectedSheetIndex( $index );

        return $this;
    }

    public function noHeading( $skipHeading = true )
    {
        $this->setSkipRows( $skipHeading ? 1 : 0 );

        return $this;
    }
}
