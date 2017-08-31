<?php

namespace RodrigoPedra\RecordProcessor\Readers;

use Illuminate\Support\Collection;
use RodrigoPedra\RecordProcessor\Traits\CountsLines;

class CollectionReader extends IteratorReader
{
    use CountsLines;

    public function __construct( Collection $collection )
    {
        parent::__construct( $collection->getIterator() );
    }
}
