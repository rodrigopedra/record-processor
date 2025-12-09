<?php

namespace RodrigoPedra\RecordProcessor\Examples\RecordObjects;

use Illuminate\Support\Arr;
use RodrigoPedra\RecordProcessor\Contracts\Reader;
use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\RecordParser;
use RodrigoPedra\RecordProcessor\Records\NullRecord;
use RodrigoPedra\RecordProcessor\Records\SimpleRecord;

class ExampleRecordParser implements RecordParser
{
    public function parseRecord(Reader $reader, $rawContent): Record
    {
        if (\is_string($rawContent)) {
            $rawContent = \explode('|', $rawContent);
        }

        $values = \array_values(Arr::wrap($rawContent));

        if (\count($values) < 2) {
            return NullRecord::get();
        }

        [$name, $email,] = $values;

        return new SimpleRecord([
            'name' => $name,
            'email' => $email,
        ]);
    }
}
