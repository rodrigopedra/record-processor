<?php

namespace RodrigoPedra\RecordProcessor;

use InvalidArgumentException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use RodrigoPedra\RecordProcessor\Contracts\ProcessorStage;
use RodrigoPedra\RecordProcessor\Traits\BuilderConcerns;

class ProcessorBuilder implements LoggerAwareInterface
{
    use BuilderConcerns\BuildsSource,
        BuilderConcerns\BuildsReaders,
        BuilderConcerns\BuildsCompilers,
        BuilderConcerns\BuildsWriters,
        BuilderConcerns\BuildsFormatter,
        BuilderConcerns\BuildsStages;

    /** @var  LoggerInterface */
    protected $logger;

    /** @var ProcessorStage[] */
    protected $stages = [];

    public function build()
    {
        $source = $this->makeSource();

        $converter = new Processor( $source );

        foreach ($this->stages as $stage) {
            $converter->addStage( $stage );
        }

        return $converter;
    }

    public function addStage( ProcessorStage $stage )
    {
        $this->stages[] = $stage;

        return $this;
    }

    public function setLogger( LoggerInterface $logger = null )
    {
        if (is_null( $logger )) {
            return $this;
        }

        $this->logger = $logger;

        return $this;
    }

    protected function getLogger()
    {
        if (is_null( $this->logger )) {
            throw new InvalidArgumentException( 'Missing Logger instance. Use setLogger(...) to provide a Logger' );
        }

        return $this->logger;
    }
}
