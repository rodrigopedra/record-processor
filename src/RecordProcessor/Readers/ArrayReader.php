<?php

namespace RodrigoPedra\RecordProcessor\Readers;

use ArrayIterator;
use RodrigoPedra\RecordProcessor\Traits\CountsLines;
use RodrigoPedra\RecordProcessor\Traits\ReaderInnerIterator;

class ArrayReader extends IteratorReader
{
    use CountsLines, ReaderInnerIterator;

    public function __construct( array $items )
    {
        parent::__construct( new ArrayIterator( $items ) );
    }
}
