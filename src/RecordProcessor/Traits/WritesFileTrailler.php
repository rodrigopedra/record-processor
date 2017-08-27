<?php

namespace RodrigoPedra\RecordProcessor\Traits;

use RodrigoPedra\RecordProcessor\Helpers\WriterCallbackProxy;
use function RodrigoPedra\RecordProcessor\value_or_null;

trait WritesFileTrailler
{
    protected function writeTrailler()
    {
        $trailler = $this->getTrailler();

        if (is_null( $trailler )) {
            return;
        }

        $content = is_callable( $trailler )
            ? call_user_func_array( $trailler, [ new WriterCallbackProxy( $this->writer, $this->getRecordCount() ) ] )
            : $trailler;

        $content = value_or_null( $content );

        if (is_null( $content )) {
            return;
        }

        $this->writer->append( $content );
    }
}
