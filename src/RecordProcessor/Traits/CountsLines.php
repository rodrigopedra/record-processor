<?php

namespace RodrigoPedra\RecordProcessor\Traits;

trait CountsLines
{
    /** @var int */
    protected $lineCount = 0;

    public function getLineCount()
    {
        return $this->lineCount;
    }

    protected function incrementLineCount($amount = 1)
    {
        $this->lineCount += $amount;
    }
}
