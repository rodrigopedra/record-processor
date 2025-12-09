<?php

namespace RodrigoPedra\RecordProcessor\RecordParsers;

use Illuminate\Contracts\Support\Arrayable;
use RodrigoPedra\RecordProcessor\Contracts\Reader;
use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\RecordParser;
use RodrigoPedra\RecordProcessor\Records\SimpleRecord;

class ArrayRecordParser implements RecordParser
{
    public function parseRecord(Reader $reader, $rawContent): Record
    {
        if ($rawContent instanceof Arrayable) {
            $rawContent = $rawContent->toArray();
        }

        if ($rawContent instanceof \stdClass) {
            $rawContent = (array) $rawContent;
        }

        if (! \is_array($rawContent)) {
            throw new \InvalidArgumentException('Content for ArrayRecordParser should be an array');
        }

        return new SimpleRecord($rawContent);
    }
}
