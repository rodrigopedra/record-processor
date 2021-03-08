<?php

namespace RodrigoPedra\RecordProcessor\Reader;

class ArrayReader extends IteratorReader
{
    public function __construct(array $items)
    {
        parent::__construct(new \ArrayIterator($items));
    }
}
