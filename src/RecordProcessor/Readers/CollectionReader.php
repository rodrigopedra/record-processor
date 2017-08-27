<?php

namespace RodrigoPedra\RecordProcessor\Readers;

use Illuminate\Support\Collection;
use RodrigoPedra\RecordProcessor\Traits\CountsLines;
use RodrigoPedra\RecordProcessor\Traits\ReaderInnerIterator;

class CollectionReader extends IteratorReader
{
    use CountsLines, ReaderInnerIterator;

    public function __construct( Collection $collection )
    {
        parent::__construct( $collection->getIterator() );
    }
}
