<?php

namespace RodrigoPedra\RecordProcessor\Records\Formatter;

use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\RecordFormatter;
use RodrigoPedra\RecordProcessor\Contracts\Writer;

class LogRecordFormatter implements RecordFormatter
{
    protected $logValid = true;

    public function __construct( $logValid = true )
    {
        $this->logValid = $logValid;
    }

    public function formatRecord( Writer $writer, Record $record )
    {
        if ($this->logValid xor $record->valid()) {
            return false;
        }

        $writer->append( $record->toArray() );

        return true;
    }

    protected function mapRecords( array $records )
    {
        return array_map( function ( Record $record ) { return $record->toArray(); }, $records );
    }
}
