<?php

namespace RodrigoPedra\RecordProcessor;

use RuntimeException;
use League\Pipeline\PipelineBuilder;
use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Traits\CountsRecords;
use RodrigoPedra\RecordProcessor\Contracts\ProcessorStage;
use RodrigoPedra\RecordProcessor\Contracts\ProcessorStageFlusher;
use RodrigoPedra\RecordProcessor\Contracts\ProcessorStageHandler;
use RodrigoPedra\RecordProcessor\Helpers\StopOnNullPipelineProcessor;
use RodrigoPedra\RecordProcessor\Stages\TransferObjects\FlushPayload;
use RodrigoPedra\RecordProcessor\Stages\TransferObjects\ProcessorOutput;
use RodrigoPedra\RecordProcessor\Contracts\Processor as ProcessorContract;

class Processor implements ProcessorContract
{
    use CountsRecords;

    /** @var  Source */
    protected $source;

    /** @var  PipelineBuilder */
    protected $stages;

    /** @var  PipelineBuilder */
    protected $flushers;

    public function __construct(Source $source)
    {
        $this->source = $source;
        $this->stages = new PipelineBuilder;
        $this->flushers = new PipelineBuilder;
    }

    public function process()
    {
        $this->recordCount = 0;

        try {
            $this->source->open();

            /** @var \League\Pipeline\Pipeline $stages */
            $stages = $this->stages->build(new StopOnNullPipelineProcessor);

            $this->recordCount = 0;

            foreach ($this->source as $records) {
                foreach ($records as $record) {
                    if (! $record instanceof Record) {
                        throw new RuntimeException('Record parser should return or generate a Record instance');
                    }

                    if ($record->valid()) {
                        $this->incrementRecordCount();
                    }

                    $stages->process($record);
                }
            }

            /** @var \League\Pipeline\Pipeline $flushers */
            $flushers = $this->flushers->build();

            /** @var FlushPayload $payload */
            $payload = $flushers->process(new FlushPayload);

            $results = new ProcessorOutput(
                $this->source->getLineCount(),
                $this->getRecordCount(),
                $payload->getLineCount(),
                $payload->getRecordCount(),
                $payload->getOutput()
            );

            return $results;
        } finally {
            $this->source->close();
        }
    }

    public function addStage(ProcessorStage $stage)
    {
        if ($stage instanceof ProcessorStageHandler) {
            $this->addProcessorStageHandler($stage);
        }

        if ($stage instanceof ProcessorStageFlusher) {
            $this->addProcessorStageFlusher($stage);
        }
    }

    protected function addProcessorStageHandler(ProcessorStageHandler $stage)
    {
        $this->stages->add(function (Record $record = null) use ($stage) {
            return $stage->handle($record);
        });
    }

    protected function addProcessorStageFlusher(ProcessorStageFlusher $stage)
    {
        $this->flushers->add(function (FlushPayload $payload) use ($stage) {
            return $stage->flush($payload);
        });
    }
}
