<?php

namespace RodrigoPedra\RecordProcessor\RecordSerializers;

use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\RecordSerializer;
use RodrigoPedra\RecordProcessor\Contracts\Serializer;

class ArrayRecordSerializer implements RecordSerializer
{
    protected bool $writesValidRecords = true;

    public function __construct(bool $writesValidRecords = true)
    {
        $this->writesValidRecords = $writesValidRecords;
    }

    public function serializeRecord(Serializer $serializer, Record $record): bool
    {
        if ($this->writesValidRecords xor $record->isValid()) {
            return false;
        }

        $serializer->append($record->toArray());

        return true;
    }
}
