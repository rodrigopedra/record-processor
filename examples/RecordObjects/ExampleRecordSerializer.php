<?php

namespace RodrigoPedra\RecordProcessor\Examples\RecordObjects;

use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\RecordSerializer;
use RodrigoPedra\RecordProcessor\Contracts\Serializer;
use RodrigoPedra\RecordProcessor\Serializers\TextFileSerializer;

class ExampleRecordSerializer implements RecordSerializer
{
    public function serializeRecord(Serializer $serializer, Record $record): bool
    {
        /** @var  \RodrigoPedra\RecordProcessor\Examples\RecordObjects\ExampleRecord $record */
        if (! $record->isValid()) {
            return false;
        }

        $content = $serializer instanceof TextFileSerializer
            ? $record->toText()
            : $record->toArray();

        $serializer->append($content);

        return true;
    }
}
