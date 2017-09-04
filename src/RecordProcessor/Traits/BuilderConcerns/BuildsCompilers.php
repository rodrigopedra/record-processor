<?php

namespace RodrigoPedra\RecordProcessor\Traits\BuilderConcerns;

use RodrigoPedra\RecordProcessor\Contracts\Writer;
use RodrigoPedra\RecordProcessor\Helpers\WriterConfigurator;
use RodrigoPedra\RecordProcessor\Stages\Compiler;
use RodrigoPedra\RecordProcessor\Stages\DeferredStageBuilder;

trait BuildsCompilers
{
    protected function addCompiler( Writer $writer, WriterConfigurator $writerConfigurator = null )
    {
        $compilerBuilder = function () use ( $writer, $writerConfigurator ) {
            if (is_null( $writerConfigurator )) {
                return new Compiler( $writer, $this->getRecordFormatter() );
            }

            $recordFormatter = $writerConfigurator->getRecordFormatter( $this->getRecordFormatter() );

            $compiler = new Compiler( $writer, $recordFormatter );

            $compiler->setHeader( $writerConfigurator->getHeader() );
            $compiler->setTrailler( $writerConfigurator->getTrailler() );

            return $compiler;
        };

        if (is_null( $this->recordFormatter )) {
            $this->addStage( new DeferredStageBuilder( $compilerBuilder ) );

            return $this;
        }

        $this->addStage( $compilerBuilder() );

        return $this;
    }
}
