<?php

namespace RodrigoPedra\RecordProcessor\Records\Formatter;

use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\RecordFormatter;
use RodrigoPedra\RecordProcessor\Contracts\Writer;

class ArrayRecordFormatter implements RecordFormatter
{
    public function formatRecord( Writer $writer, Record $record )
    {
        if (!$record->valid()) {
            return false;
        }

        $writer->append( $record->toArray() );

        return true;
    }
}
