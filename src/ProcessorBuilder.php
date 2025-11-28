<?php

namespace RodrigoPedra\RecordProcessor;

use Illuminate\Contracts\Container\Container;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Psr\Log\NullLogger;
use RodrigoPedra\RecordProcessor\Concerns\Builder;
use RodrigoPedra\RecordProcessor\Contracts\ProcessorStage;

class ProcessorBuilder implements LoggerAwareInterface, LoggerInterface
{
    use Builder\BuildsParser;
    use Builder\BuildsStages;
    use Builder\BuildsSerializers;
    use LoggerAwareTrait;
    use LoggerTrait;

    protected ?Container $container = null;

    /** @var \RodrigoPedra\RecordProcessor\Contracts\ProcessorStage[]|string[] */
    protected array $stages = [];

    public function __construct()
    {
        $this->setLogger(new NullLogger());
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
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

    public function addStage(ProcessorStage|string $stage): static
    {
        $this->stages[] = $stage;

        return $this;
    }

    public function withContainer(Container $container): static
    {
        $this->container = $container;

        return $this;
    }

    protected function logger(): LoggerInterface
    {
        if (\is_null($this->logger)) {
            throw new \RuntimeException('Missing Logger instance. Use setLogger(...) to provide an instance');
        }

        return $this->logger;
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
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

    public function log($level, \Stringable|string $message, array $context = []): void
    {
        $this->logger?->log($level, $message, $context);
    }
}
