<?php

namespace RodrigoPedra\RecordProcessor\Serializers;

use Illuminate\Support\Arr;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\IWriter;
use RodrigoPedra\RecordProcessor\Configurators\Serializers\ExcelFileSerializerConfigurator;
use RodrigoPedra\RecordProcessor\Support\Excel\WorkbookConfigurator;
use RodrigoPedra\RecordProcessor\Support\Excel\WorksheetConfigurator;
use RodrigoPedra\RecordProcessor\Support\FileInfo;

class ExcelFileSerializer extends FileSerializer
{
    protected const ROW_LIMIT = 1048576;

    protected ?IWriter $writer = null;
    protected ?Spreadsheet $workbook = null;

    public function __construct(\SplFileObject|string $file)
    {
        parent::__construct($file);

        if ($this->fileInfo->isTempFile()) {
            throw new \RuntimeException('Cannot write Excel as a temporary file');
        }

        $this->file = null;
        $this->configurator = new ExcelFileSerializerConfigurator($this, true, true);
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function open()
    {
        $this->lineCount = 0;
        $this->file = null;

        $this->writer = $this->createWriter();
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function close()
    {
        $this->writer->save($this->fileInfo->getRealPath());
        $this->writer = null;

        $this->file = FileInfo::createReadableFileObject($this->fileInfo->getRealPath());
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function append($content): void
    {
        if ($this->lineCount() === static::ROW_LIMIT) {
            throw new \RuntimeException(
                \vsprintf('Excel worksheet cannot contain more than %d rows', [
                    \number_format(static::ROW_LIMIT, '0'),
                ])
            );
        }

        $worksheet = $this->workbook->getActiveSheet();

        $this->appendRowToWorksheet($worksheet, Arr::wrap($content));

        $this->incrementLineCount();
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    protected function createWriter(): IWriter
    {
        $extension = $this->fileInfo->getExtension();

        if (\in_array($extension, ['xls', 'xlt'])) {
            $this->workbook = $this->createWorkbook();

            return IOFactory::createWriter($this->workbook, 'Xls');
        }

        if (\in_array($extension, ['xlsx', 'xlsm', 'xltx', 'xltm'])) {
            $this->workbook = $this->createWorkbook();

            return IOFactory::createWriter($this->workbook, 'Xlsx');
        }

        throw new \RuntimeException('The file must have a valid Excel extension');
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    protected function createWorkbook(): Spreadsheet
    {
        $workbook = new Spreadsheet();

        $this->configureWorkbook($workbook);

        $workbook->setActiveSheetIndex(0);

        $worksheet = $workbook->getActiveSheet();

        $worksheet->setSelectedCell('A1');

        $this->configureWorksheet($worksheet);

        return $workbook;
    }

    protected function configureWorkbook(Spreadsheet $workbook)
    {
        $configurator = $this->configurator->workbookConfigurator();

        if (\is_null($configurator)) {
            return;
        }

        \call_user_func($configurator, new WorkbookConfigurator($workbook));
    }

    protected function configureWorksheet(Worksheet $worksheet)
    {
        $configurator = $this->configurator->worksheetConfigurator();

        if (\is_null($configurator)) {
            return;
        }

        \call_user_func($configurator, new WorksheetConfigurator($worksheet));

        $worksheet->setSelectedCell('A1');
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    protected function appendRowToWorksheet(Worksheet $worksheet, array $values)
    {
        $currentCell = $worksheet->getActiveCell();

        [$column, $row] = Coordinate::coordinateFromString($currentCell);
        $row = \intval($row);

        $startColumn = Coordinate::columnIndexFromString($column);

        foreach (\array_values($values) as $index => $value) {
            $currentColumn = Coordinate::stringFromColumnIndex($startColumn + $index);

            $worksheet->setCellValue($currentColumn . $row, $value);
        }

        $worksheet->setSelectedCell($column . ($row + 1));
    }
}
