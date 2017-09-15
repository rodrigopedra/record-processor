<?php

namespace RodrigoPedra\RecordProcessor\Traits\Writers;

use RodrigoPedra\RecordProcessor\Contracts\Record;

trait WritesHeader
{
    protected function writeHeader( Record $firstRecord = null )
    {
        /** @var \RodrigoPedra\RecordProcessor\Helpers\Writers\WriterAddon|null $header */
        $header = $this->getHeader();

        if (is_null( $header )) {
            return;
        }

        $header->handle( $this->writer, $this->getRecordCount(), $firstRecord );
    }
}
