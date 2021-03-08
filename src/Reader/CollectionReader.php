<?php

namespace RodrigoPedra\RecordProcessor\Reader;

use Illuminate\Support\Collection;

class CollectionReader extends IteratorReader
{
    public function __construct(Collection $collection)
    {
        parent::__construct($collection->getIterator());
    }
}
