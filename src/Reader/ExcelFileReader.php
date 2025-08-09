<?php

namespace RodrigoPedra\RecordProcessor\Reader;

use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\IOFactory;
use RodrigoPedra\RecordProcessor\Configurators\Readers\ExcelFileReaderConfigurator;
use RodrigoPedra\RecordProcessor\RecordParsers\ArrayRecordParser;

class ExcelFileReader extends FileReader
{
    protected int $skipRows = 0;
    protected int $selectedSheetIndex = 0;
    protected ExcelFileReaderConfigurator $configurator;

    public function __construct(\SplFileObject|string $file)
    {
        parent::__construct($file);

        $this->configurator = new ExcelFileReaderConfigurator($this);
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function open()
    {
        parent::open();

        $spreadsheet = IOFactory::load($this->file->getRealPath());

        $spreadsheet->setActiveSheetIndex($this->selectedSheetIndex());

        // RowIterators starts at 1
        $iterator = $spreadsheet->getActiveSheet()->getRowIterator($this->skipRows() + 1);

        $this->withInnerIterator($iterator);
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Calculation\Exception
     */
    public function current(): array
    {
        /** @var  \PhpOffice\PhpSpreadsheet\Worksheet\Row $row */
        $row = $this->iteratorCurrent();

        $cells = Collection::make();

        $cellsIterator = $row->getCellIterator();
        $cellsIterator->setIterateOnlyExistingCells(false);

        /** @var  \PhpOffice\PhpSpreadsheet\Cell\Cell $cell */
        foreach ($cellsIterator as $cell) {
            $column = $cell->getColumn();
            $value = $cell->getCalculatedValue();

            $cells->put($column, $value);
        }

        return $cells->toArray();
    }

    public function skipRows(): int
    {
        return $this->skipRows;
    }

    public function selectedSheetIndex(): int
    {
        return $this->selectedSheetIndex;
    }

    public function withSkipRows(int $rows): static
    {
        $this->skipRows = $rows;

        return $this;
    }

    public function withSelectedSheetIndex(int $index): static
    {
        $this->selectedSheetIndex = $index;

        return $this;
    }

    public function noHeading(bool $skipHeading = true): static
    {
        $this->withSkipRows($skipHeading ? 1 : 0);

        return $this;
    }

    public function configurator(): ExcelFileReaderConfigurator
    {
        return $this->configurator;
    }

    public function defaultRecordParser(): ArrayRecordParser
    {
        return new ArrayRecordParser();
    }
}
