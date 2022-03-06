<?php

namespace RodrigoPedra\RecordProcessor\Concerns;

trait CountsLines
{
    protected int $lineCount = 0;

    public function lineCount(): int
    {
        return $this->lineCount;
    }

    protected function incrementLineCount(int $amount = 1): static
    {
        $this->lineCount += $amount;

        return $this;
    }
}
