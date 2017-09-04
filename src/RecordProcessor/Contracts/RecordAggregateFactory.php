<?php

namespace RodrigoPedra\RecordProcessor\Contracts;

interface RecordAggregateFactory
{
    /**
     * @param Record $master
     *
     * @return Record
     */
    public function makeRecordAggregate( Record $master );
}
