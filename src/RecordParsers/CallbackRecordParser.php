<?php

namespace RodrigoPedra\RecordProcessor\RecordParsers;

use RodrigoPedra\RecordProcessor\Contracts\Reader;
use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\RecordParser;

class CallbackRecordParser implements RecordParser
{
    protected readonly \Closure $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback(...);
    }

    public function parseRecords(Reader $reader, $rawContent): Record|iterable
    {
        return \call_user_func($this->callback, $rawContent);
    }
}
