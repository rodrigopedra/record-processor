<?php

namespace RodrigoPedra\RecordProcessor;

use Illuminate\Contracts\Container\Container;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use RodrigoPedra\RecordProcessor\Concerns\Builder;
use RodrigoPedra\RecordProcessor\Contracts\ProcessorStage;

class ProcessorBuilder implements LoggerAwareInterface
{
    use Builder\BuildsParser;
    use Builder\BuildsStages;
    use Builder\BuildsSerializers;

    protected ?Container $container = null;
    protected LoggerInterface $logger;

    /** @var \RodrigoPedra\RecordProcessor\Contracts\ProcessorStage|string[] */
    protected array $stages = [];

    public function build(): Processor
    {
        $parser = $this->makeParser();

        $processor = new Processor($this->container, $parser);

        foreach ($this->stages as $stage) {
            if (\is_string($stage)) {
                $stage = $this->buildStage($stage);
            }

            $processor->addStage($stage);
        }

        return $processor;
    }

    /**
     * @param  \RodrigoPedra\RecordProcessor\Contracts\ProcessorStage|string  $stage
     * @return $this
     */
    public function addStage($stage): ProcessorBuilder
    {
        $this->stages[] = $stage;

        return $this;
    }

    public function withContainer(Container $container): self
    {
        $this->container = $container;

        return $this;
    }

    public function setLogger(LoggerInterface $logger = null): ProcessorBuilder
    {
        if (\is_null($logger)) {
            return $this;
        }

        $this->logger = $logger;

        return $this;
    }

    protected function logger(): LoggerInterface
    {
        if (\is_null($this->logger)) {
            throw new \RuntimeException('Missing Logger instance. Use setLogger(...) to provide an instance');
        }

        return $this->logger;
    }

    protected function buildStage(string $stage): ProcessorStage
    {
        if (! \class_exists($stage)) {
            throw new \RuntimeException("'{$stage}' should be an instance of " . ProcessorStage::class);
        }

        if (\is_null($this->container)) {
            return new $stage();
        }

        return $this->container->make($stage);
    }
}
