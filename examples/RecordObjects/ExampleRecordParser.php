<?php

namespace RodrigoPedra\RecordProcessor\Examples\RecordObjects;

use Illuminate\Support\Arr;
use RodrigoPedra\RecordProcessor\Contracts\Reader;
use RodrigoPedra\RecordProcessor\Records\InvalidRecord;
use RodrigoPedra\RecordProcessor\Contracts\RecordParser;

class ExampleRecordParser implements RecordParser
{
    public function parseRecord(Reader $reader, $rawContent)
    {
        if (is_string($rawContent)) {
            $rawContent = explode('|', $rawContent);
        }

        $values = Arr::wrap($rawContent);

        if (count($values) < 2) {
            return new InvalidRecord;
        }

        return new ExampleRecord([
            'name' => reset($values),
            'email' => end($values),
        ]);
    }
}
