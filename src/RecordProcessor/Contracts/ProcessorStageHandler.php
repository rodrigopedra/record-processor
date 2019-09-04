<?php

namespace RodrigoPedra\RecordProcessor\Contracts;

interface ProcessorStageHandler extends ProcessorStage
{
    /**
     * @param  Record  $record
     * @return Record|null
     */
    public function handle(Record $record);
}
