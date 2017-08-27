<?php

namespace RodrigoPedra\RecordProcessor\Traits;

use Iterator;

trait ReaderInnerIterator
{
    /** @var Iterator|null */
    protected $iterator = null;

    public function current()
    {
        return $this->iterator->current();
    }

    public function next()
    {
        $this->iterator->next();
    }

    public function key()
    {
        return $this->lineCount;
    }

    public function valid()
    {
        $valid = !is_null( $this->iterator ) && $this->iterator->valid();

        if ($valid) {
            $this->incrementLineCount();
        }

        return $valid;
    }

    public function rewind()
    {
        $this->close();
        $this->open();

        $this->iterator->rewind();
    }

    public function getInnerIterator()
    {
        return $this->iterator;
    }

    protected function setInnerIterator( Iterator $iterator = null )
    {
        $this->iterator = $iterator;
    }
}
