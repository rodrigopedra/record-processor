<?php

namespace RodrigoPedra\RecordProcessor\Writers;

use InvalidArgumentException;
use RodrigoPedra\RecordProcessor\Contracts\ConfigurableWriter;
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

    /** @var  \Maatwebsite\Excel\Writers\LaravelExcelWriter|null */
    protected $writer = null;

    public function __construct( $file )
    {
        parent::__construct( $file );

        if ($this->fileInfo->isTempFile()) {
            throw new InvalidArgumentException( 'Cannot write Excel to a temporary file' );
        }

        $this->file = null;
    }

    public function open()
    {
        $this->lineCount = 0;
        $this->file      = null;

        $excel = app( 'excel' );

        $this->writer = $excel->create( $this->fileInfo->getBasenameWithoutExtension(),
            $this->getWorkbookConfigurator() );
        $this->writer->sheet( 'Worksheet', $this->getWorksheetConfigurator() );
    }

    public function close()
    {
        $this->writer->store( $this->fileInfo->getExtension(), $this->fileInfo->getPath() );
        $this->writer = null;

        $this->file = FileInfo::createReadableFileObject( $this->fileInfo->getRealPath() );
    }

    /**
     * @param  array $content
     *
     * @return void
     */
    public function append( $content )
    {
        if ($this->getLineCount() === static::ROW_LIMIT) {
            throw new RuntimeException( sprintf( 'Excel worksheet cannot contain more than %d rows',
                number_format( static::ROW_LIMIT, '0', '.', ',' )
            ) );
        }

        $this->writer->getSheet()->appendRow( array_wrap( $content ) );

        $this->incrementLineCount();
    }
}
