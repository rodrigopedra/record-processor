<?php

namespace RodrigoPedra\RecordProcessor\Stages;

use RodrigoPedra\RecordProcessor\Contracts\ProcessorStage;
use RodrigoPedra\RecordProcessor\Contracts\Record;

class ValidRecords implements ProcessorStage
{
    public function handle( Record $record )
    {
        return $record->valid() ? $record : null;
    }
}
