<?php

namespace RodrigoPedra\RecordProcessor\Writers;

use RodrigoPedra\RecordProcessor\Contracts\ConfigurableWriter;
use RodrigoPedra\RecordProcessor\Traits\ConfiguresExcelWriter;
use RuntimeException;

class ExcelFileWriter extends FileWriter implements ConfigurableWriter
{
    use ConfiguresExcelWriter;

    const ROW_LIMIT = 1048576;

    /** @var \Maatwebsite\Excel\Writers\LaravelExcelWriter|null */
    protected $writer = null;

    public function open()
    {
        $this->lineCount = 0;

        $excel = app( 'excel' );

        $this->writer = $excel->create( $this->getBasenameWithoutExtension(), $this->getWorkbookConfigurator() );

        $this->writer->sheet( 'Worksheet', $this->getWorksheetConfigurator() );
    }

    public function close()
    {
        $this->writer->store( $this->getExtension(), $this->getPath() );

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

        $this->writer->getSheet()->appendRow( array_wrap( $content ) );

        $this->incrementLineCount();
    }
}
