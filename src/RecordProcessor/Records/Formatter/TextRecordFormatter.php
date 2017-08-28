<?php

namespace RodrigoPedra\RecordProcessor\Records\Formatter;

use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\RecordFormatter;
use RodrigoPedra\RecordProcessor\Contracts\TextRecord;
use RodrigoPedra\RecordProcessor\Contracts\Writer;
use RuntimeException;

class TextRecordFormatter implements RecordFormatter
{
    public function formatRecord( Writer $writer, Record $record )
    {
        if (!$record instanceof TextRecord) {
            $className = get_class( $record );

            throw new RuntimeException( "'{$className}' should implement TextRecord interface" );
        }

        if (!$record->valid()) {
            return false;
        }

        $writer->append( $record->toText() ?: '' );

        return true;
    }
}
