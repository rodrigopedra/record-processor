<?php

namespace RodrigoPedra\RecordProcessor\Contracts;

use OuterIterator;

interface Reader extends Resource, OuterIterator
{
    /**
     * @return  int
     */
    public function getLineCount();
}
