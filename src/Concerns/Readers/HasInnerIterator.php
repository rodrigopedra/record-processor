<?php

namespace RodrigoPedra\RecordProcessor\Concerns\Readers;

trait HasInnerIterator
{
    protected ?\Iterator $iterator = null;

    public function current(): mixed
    {
        return $this->iterator->current();
    }

    public function next(): void
    {
        $this->iterator->next();
    }

    public function key(): int
    {
        return $this->lineCount;
    }

    public function valid(): bool
    {
        $valid = $this->iterator?->valid() ?? false;

        if ($valid) {
            $this->incrementLineCount();
        }

        return $valid;
    }

    public function rewind(): void
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
