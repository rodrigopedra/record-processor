<?php

namespace RodrigoPedra\RecordProcessor\Stages;

use RodrigoPedra\RecordProcessor\Contracts\ProcessorStageFlusher;
use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Records\RecordKeyAggregate;
use RodrigoPedra\RecordProcessor\Stages\TransferObjects\FlushPayload;

class RecordKeyAggregator implements ProcessorStageFlusher
{
    /** @var  RecordKeyAggregate|null */
    protected $aggregateRecord = null;

    /**
     * @param  Record $record
     *
     * @return Record null
     */
    public function handle( Record $record )
    {
        if (is_null( $this->aggregateRecord )) {
            $this->setAggregateRecord( $record ); // first record

            return null;
        }

        if ($this->aggregateRecord->pushRecord( $record )) {
            return null;
        }

        return $this->setAggregateRecord( $record );
    }

    /**
     * @param  FlushPayload $payload
     *
     * @return FlushPayload
     */
    public function flush( FlushPayload $payload )
    {
        $payload->setRecord( $this->aggregateRecord );

        return $payload;
    }

    protected function setAggregateRecord( Record $record )
    {
        if (!$record->valid()) {
            return null;
        }

        $current = $this->aggregateRecord;

        $this->aggregateRecord = new RecordKeyAggregate( $record );

        return $current;
    }
}

