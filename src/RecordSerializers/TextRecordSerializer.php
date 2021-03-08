<?php

namespace RodrigoPedra\RecordProcessor\RecordSerializers;

use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\RecordSerializer;
use RodrigoPedra\RecordProcessor\Contracts\TextRecord;
use RodrigoPedra\RecordProcessor\Contracts\Serializer;

class TextRecordSerializer implements RecordSerializer
{
    protected bool $writesValidRecords = true;

    public function __construct(bool $writesValidRecords = true)
    {
        $this->writesValidRecords = $writesValidRecords;
    }

    public function serializeRecord(Serializer $serializer, Record $record): bool
    {
        if (! $record instanceof TextRecord) {
            $className = \get_class($record);

            throw new \RuntimeException("'{$className}' should implement TextRecord interface");
        }

        if ($this->writesValidRecords xor $record->isValid()) {
            return false;
        }

        $serializer->append($record->toText());

        return true;
    }
}
