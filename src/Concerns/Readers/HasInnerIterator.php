<?php

namespace RodrigoPedra\RecordProcessor\Concerns\Readers;

trait HasInnerIterator
{
    protected ?\Iterator $iterator = null;

    public function current()
    {
        return $this->iterator->current();
    }

    public function next()
    {
        $this->iterator->next();
    }

    public function key(): int
    {
        return $this->lineCount;
    }

    public function valid(): bool
    {
        $valid = ! \is_null($this->iterator) && $this->iterator->valid();

        if ($valid) {
            $this->incrementLineCount();
        }

        return $valid;
    }

    public function rewind()
    {
        $this->lineCount = 0;

        $this->iterator->rewind();
    }

    public function getInnerIterator(): ?\Iterator
    {
        return $this->iterator;
    }

    protected function withInnerIterator(?\Iterator $iterator): static
    {
        $this->iterator = $iterator;

        return $this;
    }
}
