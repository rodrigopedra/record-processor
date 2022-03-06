<?php

namespace RodrigoPedra\RecordProcessor\Concerns;

trait CountsRecords
{
    protected int $recordCount = 0;

    public function recordCount(): int
    {
        return $this->recordCount;
    }

    protected function incrementRecordCount(int $amount = 1): static
    {
        $this->recordCount += $amount;

        return $this;
    }
}
