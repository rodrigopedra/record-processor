<?php

namespace RodrigoPedra\RecordProcessor\Contracts;

interface RecordAggregate extends Record
{
    /**
     * @param  Record  $record
     * @return bool
     */
    public function pushRecord(Record $record);

    /**
     * @return  array
     */
    public function getRecords();
}
