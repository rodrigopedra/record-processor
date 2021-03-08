<?php

namespace RodrigoPedra\RecordProcessor\RecordSerializers;

use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\RecordSerializer;
use RodrigoPedra\RecordProcessor\Contracts\Serializer;

class CallbackRecordSerializer implements RecordSerializer
{
    protected $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    public function serializeRecord(Serializer $serializer, Record $record): bool
    {
        $data = \call_user_func($this->callback, $record);

        if ($data === false) {
            return false;
        }

        $serializer->append($data);

        return true;
    }
}
