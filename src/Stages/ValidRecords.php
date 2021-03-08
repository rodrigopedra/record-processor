<?php

namespace RodrigoPedra\RecordProcessor\Stages;

use RodrigoPedra\RecordProcessor\Contracts\ProcessorStageHandler;
use RodrigoPedra\RecordProcessor\Contracts\Record;

class ValidRecords implements ProcessorStageHandler
{
    public function handle(Record $record, \Closure $next): ?Record
    {
        if (! $record->isValid()) {
            return null;
        }

        return $next($record);
    }
}
