<?php

namespace RodrigoPedra\RecordProcessor\RecordSerializers;

use RodrigoPedra\RecordProcessor\Contracts\JsonRecord;
use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\RecordSerializer;
use RodrigoPedra\RecordProcessor\Contracts\Serializer;
use RodrigoPedra\RecordProcessor\Serializers\JSONFileSerializer;

class JsonRecordSerializer implements RecordSerializer
{
    protected bool $writesValidRecords = true;
    protected int $jsonEncodeOptions = 0;

    public function __construct(bool $writesValidRecords = true, int $jsonEncodeOptions = 0)
    {
        $this->writesValidRecords = $writesValidRecords;

        $this->jsonEncodeOptions = $jsonEncodeOptions > 0
            ? $jsonEncodeOptions
            : JSONFileSerializer::JSON_ENCODE_OPTIONS;
    }

    public function serializeRecord(Serializer $serializer, Record $record): bool
    {
        if (! $record instanceof JsonRecord) {
            $className = $record::class;

            throw new \RuntimeException("'{$className}' should implement JsonRecord interface");
        }

        if ($this->writesValidRecords xor $record->isValid()) {
            return false;
        }

        $serializer->append($record->toJson($this->jsonEncodeOptions));

        return true;
    }
}
