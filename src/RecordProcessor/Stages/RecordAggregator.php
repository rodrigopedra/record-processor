<?php

namespace RodrigoPedra\RecordProcessor\Stages;

use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\RecordAggregate;
use RodrigoPedra\RecordProcessor\Records\RecordKeyAggregate;
use RodrigoPedra\RecordProcessor\Contracts\ProcessorStageHandler;
use RodrigoPedra\RecordProcessor\Contracts\ProcessorStageFlusher;
use RodrigoPedra\RecordProcessor\Contracts\RecordAggregateFactory;
use RodrigoPedra\RecordProcessor\Stages\TransferObjects\FlushPayload;

class RecordAggregator implements ProcessorStageHandler, ProcessorStageFlusher, RecordAggregateFactory
{
    /** @var  RecordAggregate|null */
    protected $aggregateRecord = null;

    /** @var  RecordAggregateFactory */
    protected $recordAggregateFactory;

    public function __construct(RecordAggregateFactory $recordAggregateFactory = null)
    {
        $this->recordAggregateFactory = $recordAggregateFactory ?: $this;
    }

    /**
     * @param  Record  $record
     * @return Record null
     */
    public function handle(Record $record)
    {
        if (is_null($this->aggregateRecord)) {
            $this->setAggregateRecord($record); // first record

            return null;
        }

        if ($this->aggregateRecord->pushRecord($record)) {
            return null;
        }

        return $this->setAggregateRecord($record);
    }

    /**
     * @param  FlushPayload  $payload
     * @return FlushPayload
     */
    public function flush(FlushPayload $payload)
    {
        $payload->setRecord($this->aggregateRecord);

        return $payload;
    }

    protected function setAggregateRecord(Record $record)
    {
        if (! $record->valid()) {
            return null;
        }

        $current = $this->aggregateRecord;

        $this->aggregateRecord = $this->recordAggregateFactory->makeRecordAggregate($record);

        return $current;
    }

    public function makeRecordAggregate(Record $record)
    {
        // default RecordAggregate
        return new RecordKeyAggregate($record);
    }
}
