<?php

namespace RodrigoPedra\RecordProcessor\Records\Parsers;

use RuntimeException;
use RodrigoPedra\RecordProcessor\Contracts\Reader;
use RodrigoPedra\RecordProcessor\Records\SimpleRecord;
use RodrigoPedra\RecordProcessor\Contracts\RecordParser;

class ArrayRecordParser implements RecordParser
{
    public function parseRecord(Reader $reader, $rawContent)
    {
        if (is_object($rawContent) && method_exists($rawContent, 'toArray')) {
            $rawContent = $rawContent->toArray();
        }

        if ($rawContent instanceof \stdClass) {
            $rawContent = (array)$rawContent;
        }

        if (! is_array($rawContent)) {
            throw new RuntimeException('content for ArrayRecordParser should be an array');
        }

        return new SimpleRecord($rawContent);
    }
}
