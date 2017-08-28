<?php

namespace RodrigoPedra\RecordProcessor\Stages;

use RodrigoPedra\RecordProcessor\Contracts\ProcessorStageHandler;
use RodrigoPedra\RecordProcessor\Contracts\Record;

class ValidRecords implements ProcessorStageHandler
{
    public function handle( Record $record )
    {
        return $record->valid() ? $record : null;
    }
}
