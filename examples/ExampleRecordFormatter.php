<?php

namespace RodrigoPedra\RecordProcessor\Examples;

use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\RecordFormatter;
use RodrigoPedra\RecordProcessor\Contracts\Writer;
use RodrigoPedra\RecordProcessor\Writers\TextFileWriter;

class ExampleRecordFormatter implements RecordFormatter
{
    public function formatRecord( Writer $writer, Record $record )
    {
        if (!$record->valid()) {
            return false;
        }

        $content = $record->toArray();

        if ($writer instanceof TextFileWriter) {
            $content = implode( '|', $content );
        }

        $writer->append( $content );

        return true;
    }
}
