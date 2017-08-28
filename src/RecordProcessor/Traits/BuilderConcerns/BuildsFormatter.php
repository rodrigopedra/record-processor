<?php

namespace RodrigoPedra\RecordProcessor\Traits\BuilderConcerns;

use RodrigoPedra\RecordProcessor\Contracts\RecordAggregateFormatter;
use RodrigoPedra\RecordProcessor\Contracts\RecordFormatter;
use RodrigoPedra\RecordProcessor\Records\Formatter\ArrayRecordFormatter;
use RodrigoPedra\RecordProcessor\Stages\RecordKeyAggregator;

trait BuildsFormatter
{
    /** @var  RecordFormatter */
    protected $recordFormatter;

    public function usingFormatter( RecordFormatter $recordFormatter = null )
    {
        $this->recordFormatter = $recordFormatter;

        return $this;
    }

    protected function getRecordFormatter()
    {
        if (is_null( $this->recordFormatter )) {
            return new ArrayRecordFormatter;
        }

        return $this->recordFormatter;
    }

    public function aggregateRecordsByKey( RecordAggregateFormatter $formatter = null )
    {
        $this->usingFormatter( $formatter );

        $this->addStage( new RecordKeyAggregator );

        return $this;
    }
}
