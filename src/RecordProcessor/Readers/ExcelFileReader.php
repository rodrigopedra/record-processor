<?php

namespace RodrigoPedra\RecordProcessor\Readers;

use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\IOFactory;
use RodrigoPedra\RecordProcessor\Contracts\ConfigurableReader;
use RodrigoPedra\RecordProcessor\Helpers\Configurator;
use RodrigoPedra\RecordProcessor\Traits\ExcelReaderConfigurations;

class ExcelFileReader extends FileReader implements ConfigurableReader
{
    use ExcelReaderConfigurations;

    public function open()
    {
        parent::open();

        /** @var  \PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet */
        $spreadsheet = IOFactory::load( $this->file->getRealPath() );

        $spreadsheet->setActiveSheetIndex( $this->getSelectedSheetIndex() );

        // RowIterators starts at 1
        $iterator = $spreadsheet->getActiveSheet()->getRowIterator( $this->getSkipRows() + 1 );

        $this->setInnerIterator( $iterator );
    }

    public function current()
    {
        /** @var  \PhpOffice\PhpSpreadsheet\Worksheet\Row $row */
        $row = $this->iteratorCurrent();

        $cells = new Collection( [] );

        $cellsIterator = $row->getCellIterator();
        $cellsIterator->setIterateOnlyExistingCells( false );

        foreach ($cellsIterator as $cell) {
            /** @var  \PhpOffice\PhpSpreadsheet\Cell\Cell $cell */
            $column = $cell->getColumn();
            $value  = $cell->getCalculatedValue();

            $cells->put( $column, $value );
        }

        return $cells->toArray();
    }

    /**
     * @return array
     */
    public function getConfigurableMethods()
    {
        return [
            'noHeading',
            'setSelectedSheetIndices',
        ];
    }

    /**
     * @return Configurator
     */
    public function createConfigurator()
    {
        return new Configurator( $this );
    }
}
