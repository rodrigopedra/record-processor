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

/**
 * @property \RodrigoPedra\RecordProcessor\Configurators\Serializers\ExcelFileSerializerConfigurator $configurator
 */
class ExcelFileSerializer extends FileSerializer
{
    protected const ROW_LIMIT = 1048576;

    protected ?IWriter $writer = null;

    protected ?Spreadsheet $workbook = null;

    public function __construct(\SplFileInfo|string|null $file = null)
    {
        parent::__construct(
            configurator: new ExcelFileSerializerConfigurator($this, true, true),
            file: $file,
        );

        if ($this->file->isTempFile()) {
            throw new \RuntimeException('Cannot write Excel as a temporary file');
        }
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function open(): void
    {
        $this->lineCount = 0;
        FileInfo::createWritableFileObject($this->file);
        $this->workbook = $this->createWorkbook();
        $this->writer = $this->createWriter($this->workbook);
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function close(): void
    {
        $this->writer->save($this->file->getRealPath());
        $this->writer = null;
        $this->workbook = null;
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
                ]),
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
    protected function createWriter(Spreadsheet $workbook): IWriter
    {
        $extension = $this->file->getExtension();

        if (\in_array($extension, ['xls', 'xlt'])) {
            return IOFactory::createWriter($workbook, 'Xls');
        }

        if (\in_array($extension, ['xlsx', 'xlsm', 'xltx', 'xltm'])) {
            return IOFactory::createWriter($workbook, 'Xlsx');
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

    protected function configureWorkbook(Spreadsheet $workbook): void
    {
        $configurator = $this->configurator->workbookConfigurator();

        if (\is_null($configurator)) {
            return;
        }

        \call_user_func($configurator, new WorkbookConfigurator($workbook));
    }

    protected function configureWorksheet(Worksheet $worksheet): void
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
    protected function appendRowToWorksheet(Worksheet $worksheet, array $values): void
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
