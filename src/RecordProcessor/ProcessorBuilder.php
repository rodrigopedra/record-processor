<?php

namespace RodrigoPedra\RecordProcessor;

use Psr\Log\LoggerInterface;
use InvalidArgumentException;
use Psr\Log\LoggerAwareInterface;
use RodrigoPedra\RecordProcessor\Traits\BuilderConcerns;
use RodrigoPedra\RecordProcessor\Contracts\ProcessorStage;
use RodrigoPedra\RecordProcessor\Stages\DeferredStageBuilder;

class ProcessorBuilder implements LoggerAwareInterface
{
    use BuilderConcerns\BuildsSource,
        BuilderConcerns\BuildsReaders,
        BuilderConcerns\BuildsFormatter,
        BuilderConcerns\BuildsWriters,
        BuilderConcerns\BuildsStages,
        BuilderConcerns\BuildsCompilers;

    /** @var  LoggerInterface */
    protected $logger;

    /** @var ProcessorStage[] */
    protected $stages = [];

    public function build()
    {
        $source = $this->makeSource();

        $converter = new Processor($source);

        foreach ($this->stages as $stage) {
            if ($stage instanceof DeferredStageBuilder) {
                // deferred stage creation
                $stage = $stage->build();
            }

            $converter->addStage($stage);
        }

        return $converter;
    }

    public function addStage(ProcessorStage $stage)
    {
        $this->stages[] = $stage;

        return $this;
    }

    public function setLogger(LoggerInterface $logger = null)
    {
        if (is_null($logger)) {
            return $this;
        }

        $this->logger = $logger;

        return $this;
    }

    protected function getLogger()
    {
        if (is_null($this->logger)) {
            throw new InvalidArgumentException('Missing Logger instance. Use setLogger(...) to provide an instance');
        }

        return $this->logger;
    }
}
