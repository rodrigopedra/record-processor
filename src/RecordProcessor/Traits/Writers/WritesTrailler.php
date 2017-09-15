<?php

namespace RodrigoPedra\RecordProcessor\Traits\Writers;

trait WritesTrailler
{
    protected function writeTrailler()
    {
        /** @var \RodrigoPedra\RecordProcessor\Helpers\Writers\WriterAddon|null $trailler */
        $trailler = $this->getTrailler();

        if (is_null( $trailler )) {
            return;
        }

        $trailler->handle( $this->writer, $this->getRecordCount() );
    }
}
