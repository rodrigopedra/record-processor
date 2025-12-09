<?php

namespace RodrigoPedra\RecordProcessor\Examples\RecordObjects;

use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\RecordAggregate;
use RodrigoPedra\RecordProcessor\Contracts\RecordSerializer;
use RodrigoPedra\RecordProcessor\Contracts\Serializer;
use RodrigoPedra\RecordProcessor\Records\KeyedRecord;

class ExampleRecordAggregateSerializer implements RecordSerializer
{
    protected ExampleRecordSerializer $recordSerializer;

    public function __construct()
    {
        $this->recordSerializer = new ExampleRecordSerializer();
    }

    public function serializeRecord(Serializer $serializer, Record $record): bool
    {
        if (! $record instanceof RecordAggregate) {
            throw new \RuntimeException(\vsprintf('Record for %s should implement %s interface', [
                self::class,
                RecordAggregate::class,
            ]));
        }

        if (! $record->isValid()) {
            return false;
        }

        $children = $this->formatChildren($record->records());

        $content = [
            'name' => $record->key(),
            'email' => $children,
        ];

        return $this->recordSerializer->serializeRecord($serializer, new KeyedRecord($record->key(), $content));
    }

    public function formatChildren(array $children): string
    {
        return \implode(', ', \array_map(static fn (ExampleRecord $record) => $record->get('email'), $children));
    }
}
