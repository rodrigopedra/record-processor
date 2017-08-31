<?php

namespace RodrigoPedra\RecordProcessor\Readers;

use ArrayIterator;
use RodrigoPedra\RecordProcessor\Traits\CountsLines;

class ArrayReader extends IteratorReader
{
    use CountsLines;

    public function __construct( array $items )
    {
        parent::__construct( new ArrayIterator( $items ) );
    }
}
