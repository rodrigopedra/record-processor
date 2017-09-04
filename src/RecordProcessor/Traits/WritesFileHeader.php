<?php

namespace RodrigoPedra\RecordProcessor\Traits;

use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Helpers\WriterCallbackProxy;
use function RodrigoPedra\RecordProcessor\value_or_null;

trait WritesFileHeader
{
    protected function writeHeader( Record $firstRecord = null )
    {
        $header = $this->getHeader();

        if (is_null( $header )) {
            return;
        }

        $content = is_callable( $header )
            ? call_user_func_array( $header, [
                new WriterCallbackProxy( $this->writer, $this->getRecordCount(), $firstRecord )
            ] )
            : $header;

        $content = value_or_null( $content );

        if (is_null( $content )) {
            return;
        }

        $this->writer->append( $content );
    }
}
