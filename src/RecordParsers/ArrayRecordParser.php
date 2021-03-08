<?php

namespace RodrigoPedra\RecordProcessor\RecordParsers;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use RodrigoPedra\RecordProcessor\Contracts\Reader;
use RodrigoPedra\RecordProcessor\Contracts\RecordParser;
use RodrigoPedra\RecordProcessor\Records\SimpleRecord;

class ArrayRecordParser implements RecordParser
{
    public function parseRecord(Reader $reader, $rawContent): SimpleRecord
    {
        if (\is_object($rawContent) && $rawContent instanceof Arrayable) {
            $rawContent = $rawContent->toArray();
        }

        if (\is_object($rawContent) && $rawContent instanceof \stdClass) {
            $rawContent = (array) $rawContent;
        }

        if (! \is_array($rawContent)) {
            throw new \InvalidArgumentException('Content for ArrayRecordParser should be an array');
        }

        return new SimpleRecord(Arr::first($rawContent), $rawContent);
    }
}
