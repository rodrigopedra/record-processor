<?php

namespace RodrigoPedra\RecordProcessor\Contracts;

use RodrigoPedra\RecordProcessor\Stages\TransferObjects\ProcessorOutput;

interface Processor
{
    /**
     * @return ProcessorOutput
     */
    public function process();
}
