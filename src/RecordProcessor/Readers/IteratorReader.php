<?php

namespace RodrigoPedra\RecordProcessor\Readers;

use Iterator;
use RodrigoPedra\RecordProcessor\Contracts\Reader;
use RodrigoPedra\RecordProcessor\Traits\CountsLines;
use RodrigoPedra\RecordProcessor\Traits\Readers\HasInnerIterator;

class IteratorReader implements Reader
{
    use CountsLines, HasInnerIterator;

    public function __construct( Iterator $iterator )
    {
        $this->setInnerIterator( $iterator );
    }

    public function open()
    {
        $this->lineCount = 0;

        $this->iterator->rewind();
    }

    public function close()
    {
        //
    }
}
