<?php

namespace RodrigoPedra\RecordProcessor;

use League\Pipeline\PipelineBuilder;
use RodrigoPedra\RecordProcessor\Contracts\Processor as ProcessorContract;
use RodrigoPedra\RecordProcessor\Contracts\ProcessorStage;
use RodrigoPedra\RecordProcessor\Contracts\ProcessorStageFlusher;
use RodrigoPedra\RecordProcessor\Contracts\ProcessorStageHandler;
use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Helpers\StopOnNullPipelineProcessor;
use RodrigoPedra\RecordProcessor\Stages\TransferObjects\FlushPayload;
use RodrigoPedra\RecordProcessor\Stages\TransferObjects\ProcessorOutput;
use RodrigoPedra\RecordProcessor\Traits\CountsRecords;

class Processor implements ProcessorContract
{
    use CountsRecords;

    /** @var  Source */
    protected $source;

    /** @var  PipelineBuilder */
    protected $stages;

    /** @var  PipelineBuilder */
    protected $flushers;

    public function __construct( Source $source )
    {
        $this->source   = $source;
        $this->stages   = new PipelineBuilder;
        $this->flushers = new PipelineBuilder;
    }

    public function process()
    {
        $this->recordCount = 0;

        try {
            $this->source->open();

            /** @var \League\Pipeline\Pipeline $stages */
            $stages = $this->stages->build( new StopOnNullPipelineProcessor );

            foreach ($this->source as $record) {
                if (is_null( $record )) {
                    continue;
                }

                $stages->process( $record );
            }

            /** @var \League\Pipeline\Pipeline $flushers */
            $flushers = $this->flushers->build();

            /** @var FlushPayload $payload */
            $payload = $flushers->process( new FlushPayload );
        } finally {
            $this->source->close();
        }

        $results = new ProcessorOutput(
            $this->source->getLineCount(),
            $this->source->getRecordCount(),
            $payload->getLineCount(),
            $payload->getRecordCount(),
            $payload->getOutput()
        );

        return $results;
    }

    public function addStage( ProcessorStage $stage )
    {
        if ($stage instanceof ProcessorStageHandler) {
            $this->addProcessorStageHandler( $stage );
        }

        if ($stage instanceof ProcessorStageFlusher) {
            $this->addProcessorStageFlusher( $stage );
        }
    }

    protected function addProcessorStageHandler( ProcessorStageHandler $stage )
    {
        $this->stages->add( function ( Record $record = null ) use ( $stage ) {
            return $stage->handle( $record );
        } );
    }

    protected function addProcessorStageFlusher( ProcessorStageFlusher $stage )
    {
        $this->flushers->add( function ( FlushPayload $payload ) use ( $stage ) {
            return $stage->flush( $payload );
        } );
    }
}
