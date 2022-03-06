<?php

namespace RodrigoPedra\RecordProcessor\Stages;

use RodrigoPedra\RecordProcessor\Contracts\ProcessorStageFlusher;
use RodrigoPedra\RecordProcessor\Contracts\ProcessorStageHandler;
use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\RecordAggregate;
use RodrigoPedra\RecordProcessor\Contracts\RecordAggregateFactory;
use RodrigoPedra\RecordProcessor\Records\RecordKeyAggregate;
use RodrigoPedra\RecordProcessor\Support\TransferObjects\FlushPayload;

class RecordAggregator implements ProcessorStageHandler, ProcessorStageFlusher, RecordAggregateFactory
{
    protected ?RecordAggregate $aggregateRecord = null;
    protected RecordAggregateFactory $recordAggregateFactory;

    public function __construct(?RecordAggregateFactory $recordAggregateFactory = null)
    {
        $this->recordAggregateFactory = $recordAggregateFactory ?? $this;
    }

    public function handle(Record $record, \Closure $next): ?Record
    {
        if (\is_null($this->aggregateRecord)) {
            $this->withAggregateRecord($record); // first record

            return null;
        }

        if ($this->aggregateRecord->addRecord($record) === true) {
            return null;
        }

        $aggregateRecord = $this->withAggregateRecord($record);

        if (\is_null($aggregateRecord)) {
            return null;
        }

        return $next($aggregateRecord);
    }

    public function flush(FlushPayload $payload, \Closure $next): FlushPayload
    {
        $payload->withRecord($this->aggregateRecord);

        return $next($payload);
    }

    protected function withAggregateRecord(Record $record): ?RecordAggregate
    {
        if (! $record->isValid()) {
            return null;
        }

        $current = $this->aggregateRecord;

        $this->aggregateRecord = $this->recordAggregateFactory->makeRecordAggregate($record);

        return $current;
    }

    public function makeRecordAggregate(Record $master): RecordKeyAggregate
    {
        return new RecordKeyAggregate($master);
    }
}
