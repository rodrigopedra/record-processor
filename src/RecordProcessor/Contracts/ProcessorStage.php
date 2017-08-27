<?php

namespace RodrigoPedra\RecordProcessor\Contracts;

interface ProcessorStage
{
    /**
     * @param  Record $record
     *
     * @return Record|null
     */
    public function handle( Record $record );
}
