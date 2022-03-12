<?php

namespace RodrigoPedra\RecordProcessor\Concerns;

trait CountsRecords
{
    private int $recordCount = 0;

    public function recordCount(): int
    {
        return $this->recordCount;
    }

    private function incrementRecordCount(int $amount = 1): static
    {
        $this->recordCount += $amount;

        return $this;
    }
}
