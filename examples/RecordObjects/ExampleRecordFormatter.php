<?php

namespace RodrigoPedra\RecordProcessor\Examples\RecordObjects;

use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\Writer;
use RodrigoPedra\RecordProcessor\Writers\TextFileWriter;
use RodrigoPedra\RecordProcessor\Contracts\RecordFormatter;

class ExampleRecordFormatter implements RecordFormatter
{
    /**
     * @param  Writer  $writer
     * @param  ExampleRecord|Record  $record
     * @return bool
     */
    public function formatRecord(Writer $writer, Record $record)
    {
        if (! $record->valid()) {
            return false;
        }

        $content = $writer instanceof TextFileWriter
            ? $record->toText()
            : $record->toArray();

        $writer->append($content);

        return true;
    }
}
