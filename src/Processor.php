<?php

namespace RodrigoPedra\RecordProcessor;

use Illuminate\Contracts\Container\Container;
use Illuminate\Pipeline\Pipeline;
use RodrigoPedra\RecordProcessor\Concerns\CountsRecords;
use RodrigoPedra\RecordProcessor\Contracts\Processor as ProcessorContract;
use RodrigoPedra\RecordProcessor\Contracts\ProcessorStage;
use RodrigoPedra\RecordProcessor\Contracts\ProcessorStageFlusher;
use RodrigoPedra\RecordProcessor\Contracts\ProcessorStageHandler;
use RodrigoPedra\RecordProcessor\Support\TransferObjects\FlushPayload;
use RodrigoPedra\RecordProcessor\Support\TransferObjects\ProcessorOutput;

final class Processor implements ProcessorContract
{
    use CountsRecords;

    private array $stages = [];
    private array $flushers = [];

    public function __construct(
        private readonly ?Container $container,
        private readonly Parser $parser,
    ) {
    }

    public function process(): ProcessorOutput
    {
        $this->recordCount = 0;

        $stages = (new Pipeline($this->container))
            ->via('handle')
            ->through($this->stages);

        $flushers = (new Pipeline($this->container))
            ->via('flush')
            ->through($this->flushers);

        try {
            $this->parser->open();
            $this->recordCount = 0;

            foreach ($this->parser as $record) {
                /** @var \RodrigoPedra\RecordProcessor\Contracts\Record|null $record */
                $record = $stages->send($record)->thenReturn();

                if ($record?->isValid()) {
                    $this->incrementRecordCount();
                }
            }

            /** @var \RodrigoPedra\RecordProcessor\Support\TransferObjects\FlushPayload $payload */
            $payload = $flushers
                ->send(new FlushPayload())
                ->thenReturn();

            return new ProcessorOutput(
                $this->parser->lineCount(),
                $this->recordCount(),
                $payload->lineCount(),
                $payload->recordCount(),
                $payload->output()
            );
        } finally {
            $this->parser->close();
        }
    }

    public function addStage(ProcessorStage $stage)
    {
        if ($stage instanceof ProcessorStageHandler) {
            $this->stages[] = $stage;
        }

        if ($stage instanceof ProcessorStageFlusher) {
            $this->flushers[] = $stage;
        }
    }
}
