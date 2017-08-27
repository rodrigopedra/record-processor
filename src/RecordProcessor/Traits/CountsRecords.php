<?php

namespace RodrigoPedra\RecordProcessor\Traits;

trait CountsRecords
{
    /** @var int */
    protected $recordCount = 0;

    public function getRecordCount()
    {
        return $this->recordCount;
    }

    protected function incrementRecordCount( $amount = 1 )
    {
        $this->recordCount += $amount;
    }
}
