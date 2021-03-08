<?php

namespace RodrigoPedra\RecordProcessor\Contracts;

interface RecordAggregateFactory
{
    public function makeRecordAggregate(Record $master): Record;
}
