<?php

namespace RodrigoPedra\RecordProcessor\Stages;

use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\ProcessorStageHandler;

class ValidRecords implements ProcessorStageHandler
{
    public function handle(Record $record)
    {
        return $record->valid() ? $record : null;
    }
}
