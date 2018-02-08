<?php

namespace RodrigoPedra\RecordProcessor\Writers;

use InvalidArgumentException;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\IWriter;
use RodrigoPedra\RecordProcessor\Contracts\ConfigurableWriter;
use RodrigoPedra\RecordProcessor\Helpers\Excel\WorksheetConfigurator;
use RodrigoPedra\RecordProcessor\Helpers\FileInfo;
use RodrigoPedra\RecordProcessor\Traits\ConfiguresExcelWriter;
use RuntimeException;

/**
 * @property  \SplFileObject|null                                 $file
 * @property  \RodrigoPedra\RecordProcessor\Helpers\FileInfo|null $fileInfo
 *
 * @package RodrigoPedra\RecordProcessor\Writers
 */
class ExcelFileWriter extends FileWriter implements ConfigurableWriter
{
    use ConfiguresExcelWriter;

    const ROW_LIMIT = 1048576;

    /** @var  IWriter|null */
    protected $writer = null;

    /** @var  Spreadsheet|null */
    protected $workbook = null;

    public function __construct( $file )
    {
        parent::__construct( $file );

        if ($this->fileInfo->isTempFile()) {
            throw new InvalidArgumentException( 'Cannot write Excel as a temporary file' );
        }

        $this->file = null;
    }

    public function open()
    {
        $this->lineCount = 0;
        $this->file      = null;

        $this->writer = $this->createWriter();
    }

    public function close()
    {
        $this->writer->save( $this->fileInfo->getRealPath() );
        $this->writer = null;

        $this->file = FileInfo::createReadableFileObject( $this->fileInfo->getRealPath() );
    }

    /**
     * @param  array $content
     *
     * @return void
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function append( $content )
    {
        if ($this->getLineCount() === static::ROW_LIMIT) {
            throw new RuntimeException( sprintf( 'Excel worksheet cannot contain more than %d rows',
                number_format( static::ROW_LIMIT, '0', '.', ',' )
            ) );
        }

        $worksheet = $this->workbook->getActiveSheet();

        $this->appendRowToWorksheet( $worksheet, array_wrap( $content ) );

        $this->incrementLineCount();
    }

    protected function createWriter()
    {
        $extension = $this->fileInfo->getExtension();

        if (in_array( $extension, [ 'xls', 'xlt' ] )) {
            $this->workbook = $this->createWorkbook();

            return IOFactory::createWriter( $this->workbook, 'Xls' );
        }

        if (in_array( $extension, [ 'xlsx', 'xlsm', 'xltx', 'xltm' ] )) {
            $this->workbook = $this->createWorkbook();

            return IOFactory::createWriter( $this->workbook, 'Xlsx' );
        }

        throw new RuntimeException( 'The file must have a valid Excel extension' );
    }

    protected function createWorkbook()
    {
        $workbook = new Spreadsheet;

        $this->configureWorkbook( $workbook );

        $workbook->setActiveSheetIndex( 0 );

        $worksheet = $workbook->getActiveSheet();

        $worksheet->setSelectedCell( 'A1' );

        $this->configureWorksheet( $worksheet );

        return $workbook;
    }

    protected function configureWorkbook( Spreadsheet $workbook )
    {
        $configurator = $this->getWorkbookConfigurator();

        if (!is_callable( $configurator )) {
            return;
        }

        call_user_func( $configurator, $workbook->getProperties() );
    }

    protected function configureWorksheet( Worksheet $worksheet )
    {
        $configurator = $this->getWorksheetConfigurator();

        if (!is_callable( $configurator )) {
            return;
        }

        call_user_func( $configurator, new WorksheetConfigurator( $worksheet ) );

        $worksheet->setSelectedCell( 'A1' );
    }

    protected function appendRowToWorksheet( Worksheet $worksheet, array $values )
    {
        $currentCell = $worksheet->getActiveCell();

        list( $column, $row ) = Coordinate::coordinateFromString( $currentCell );

        $startColumn = Coordinate::columnIndexFromString( $column );

        foreach (array_values( $values ) as $index => $value) {
            $currentColumn = Coordinate::stringFromColumnIndex( $startColumn + $index );

            $worksheet->setCellValue( $currentColumn . $row, $value );
        }

        $worksheet->setSelectedCell( $column . ( $row + 1 ) );
    }
}
