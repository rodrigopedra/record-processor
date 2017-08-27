<?php

namespace RodrigoPedra\RecordProcessor\Readers;

use Iterator;
use RodrigoPedra\RecordProcessor\Contracts\Reader;
use RodrigoPedra\RecordProcessor\Traits\CountsLines;
use RodrigoPedra\RecordProcessor\Traits\ReaderInnerIterator;

class IteratorReader implements Reader
{
    use CountsLines, ReaderInnerIterator;

    public function __construct( Iterator $iterator )
    {
        $this->setInnerIterator( $iterator );
    }

    public function open()
    {
        $this->lineCount = 0;
    }

    public function close()
    {
        //
    }
}
