<?php

namespace RodrigoPedra\RecordProcessor\Contracts;

interface RecordAggregateFactory
{
    /**
     * @param Record $master
     *
     * @return Record
     */
    public static function makeRecordAggregate( Record $master );
}
