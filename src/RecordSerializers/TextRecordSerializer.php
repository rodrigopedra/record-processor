<?php

namespace RodrigoPedra\RecordProcessor\RecordSerializers;

use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\RecordSerializer;
use RodrigoPedra\RecordProcessor\Contracts\Serializer;
use RodrigoPedra\RecordProcessor\Contracts\TextRecord;

class TextRecordSerializer implements RecordSerializer
{
    public function __construct(
        protected readonly bool $writesValidRecords = true,
    ) {}

    public function serializeRecord(Serializer $serializer, Record $record): bool
    {
        if (! $record instanceof TextRecord) {
            $className = $record::class;

            throw new \RuntimeException("'{$className}' should implement TextRecord interface");
        }

        if ($this->writesValidRecords xor $record->isValid()) {
            return false;
        }

        $serializer->append($record->toText());

        return true;
    }
}
