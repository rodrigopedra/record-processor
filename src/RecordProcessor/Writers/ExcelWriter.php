<?php

namespace RodrigoPedra\RecordProcessor\Writers;

use RodrigoPedra\RecordProcessor\Contracts\ConfigurableWriter;
use RodrigoPedra\RecordProcessor\Traits\ConfiguresExcelWriter;
use RodrigoPedra\RecordProcessor\Traits\CountsLines;
use RodrigoPedra\RecordProcessor\Traits\NoOutput;
use RuntimeException;

class ExcelWriter implements ConfigurableWriter
{
    use CountsLines, NoOutput, ConfiguresExcelWriter;

    const ROW_LIMIT = 1048576;

    /** @var \Maatwebsite\Excel\Writers\LaravelExcelWriter|null */
    protected $writer = null;

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

        $pathParts = pathinfo( $this->filepath );
        $filename  = array_get( $pathParts, 'filename' );

        $this->writer = $excel->create( $filename, $this->getWorkbookConfigurator() );

        $this->writer->sheet( 'Worksheet', $this->getWorksheetConfigurator() );
    }

    public function close()
    {
        $pathParts = pathinfo( $this->filepath );
        $path      = array_get( $pathParts, 'dirname' );
        $extension = array_get( $pathParts, 'extension', 'xlsx' );

        $this->writer->store( $extension, $path );

        $this->writer = null;
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

        $this->writer->getSheet()->appendRow( $content );

        $this->incrementLineCount();
    }
}
