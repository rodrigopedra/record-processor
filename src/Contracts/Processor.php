<?php

namespace RodrigoPedra\RecordProcessor\Contracts;

use RodrigoPedra\RecordProcessor\Support\TransferObjects\ProcessorOutput;

interface Processor
{
    public function process(): ProcessorOutput;
}
