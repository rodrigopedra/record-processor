<?php

namespace RodrigoPedra\RecordProcessor;

use InvalidArgumentException;
use Maatwebsite\Excel\Excel;
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

    /** @var Excel */
    protected $excel = null;

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

    public function setExcel( Excel $excel )
    {
        if (is_null( $excel )) {
            return $this;
        }

        $this->excel = $excel;

        return $this;
    }

    protected function getLogger()
    {
        if (is_null( $this->logger )) {
            throw new InvalidArgumentException( 'Missing Logger instance. Use setLogger(...) to provide an instance' );
        }

        return $this->logger;
    }

    protected function getExcel()
    {
        if (is_null( $this->excel )) {
            throw new InvalidArgumentException( 'Missing \Maatwebsite\Excel\Excel instance. Use setExcel(...) to provide an instance' );
        }

        return $this->excel;
    }
}
