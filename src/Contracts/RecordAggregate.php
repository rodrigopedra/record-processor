<?php

namespace RodrigoPedra\RecordProcessor\Contracts;

interface RecordAggregate extends Record
{
    public function master(): Record;

    public function addRecord(Record $record): bool;

    public function records(): iterable;
}
