<?php

namespace RodrigoPedra\RecordProcessor;

use Illuminate\Contracts\Container\Container;
use Illuminate\Pipeline\Pipeline;
use RodrigoPedra\RecordProcessor\Contracts\Processor as ProcessorContract;
use RodrigoPedra\RecordProcessor\Contracts\ProcessorStage;
use RodrigoPedra\RecordProcessor\Contracts\ProcessorStageFlusher;
use RodrigoPedra\RecordProcessor\Contracts\ProcessorStageHandler;
use RodrigoPedra\RecordProcessor\Support\TransferObjects\FlushPayload;
use RodrigoPedra\RecordProcessor\Support\TransferObjects\ProcessorOutput;

final class Processor implements ProcessorContract
{
    /** @var  \RodrigoPedra\RecordProcessor\Contracts\ProcessorStageHandler[] */
    private array $stages = [];

    /** @var  \RodrigoPedra\RecordProcessor\Contracts\ProcessorStageFlusher[] */
    private array $flushers = [];

    public function __construct(
        private readonly ?Container $container,
        private readonly Parser $parser,
    ) {}

    public function process(): ProcessorOutput
    {
        $stages = (new Pipeline($this->container))
            ->via('handle')
            ->through($this->stages);

        $flushers = (new Pipeline($this->container))
            ->via('flush')
            ->through($this->flushers);

        try {
            $this->parser->open();

            $payload = $this->run($this->parser, $stages, $flushers);

            return new ProcessorOutput(
                $this->parser->lineCount(),
                $this->parser->recordCount(),
                $payload->lineCount(),
                $payload->recordCount(),
                $payload->output(),
            );
        } finally {
            $this->parser->close();
        }
    }

    private function run(\Traversable $records, Pipeline $stages, Pipeline $flushers): FlushPayload
    {
        foreach ($records as $record) {
            $stages->send($record)->thenReturn();
        }

        return $flushers->send(new FlushPayload())->thenReturn();
    }

    public function addStage(ProcessorStage $stage): void
    {
        if ($stage instanceof ProcessorStageHandler) {
            $this->stages[] = $stage;
        }

        if ($stage instanceof ProcessorStageFlusher) {
            $this->flushers[] = $stage;
        }
    }
}
