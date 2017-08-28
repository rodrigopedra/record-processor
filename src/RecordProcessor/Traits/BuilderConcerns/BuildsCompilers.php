<?php

namespace RodrigoPedra\RecordProcessor\Traits\BuilderConcerns;

use Psr\Log\LogLevel;
use RodrigoPedra\RecordProcessor\Contracts\Writer;
use RodrigoPedra\RecordProcessor\Helpers\WriterConfigurator;
use RodrigoPedra\RecordProcessor\Records\Formatter\ArrayRecordFormatter;
use RodrigoPedra\RecordProcessor\Records\Formatter\LogRecordFormatter;
use RodrigoPedra\RecordProcessor\Stages\Compiler;
use RodrigoPedra\RecordProcessor\Writers\EchoWriter;
use RodrigoPedra\RecordProcessor\Writers\LogWriter;

trait BuildsCompilers
{
    protected function addCompiler( Writer $writer, WriterConfigurator $fileConfigurator = null )
    {
        $recordFormatter = is_null( $fileConfigurator )
            ? $this->getRecordFormatter()
            : $fileConfigurator->getRecordFormatter( $this->getRecordFormatter() );

        $compiler = new Compiler( $writer, $recordFormatter );

        if (!is_null( $fileConfigurator )) {
            $compiler->setHeader( $fileConfigurator->getHeader() );
            $compiler->setTrailler( $fileConfigurator->getTrailler() );
        }

        $this->addStage( $compiler );

        return $this;
    }

    public function logRecords( $prefix = null )
    {
        $writer = new LogWriter( $this->getLogger() );
        $writer->setLevel( LogLevel::DEBUG );
        $writer->setPrefix( $prefix );

        $compiler = new Compiler( $writer, new LogRecordFormatter );

        $this->addStage( $compiler );

        return $this;
    }

    public function logInvalidRecords( $prefix = 'INVALID' )
    {
        $writer = new LogWriter( $this->getLogger() );
        $writer->setLevel( LogLevel::ERROR );
        $writer->setPrefix( $prefix );

        $compiler = new Compiler( $writer, new LogRecordFormatter( false ) );
        $this->addStage( $compiler );

        return $this;
    }

    public function echoRecords( $prefix = null )
    {
        $writer = new EchoWriter;
        $writer->setPrefix( $prefix );

        $compiler = new Compiler( $writer, new ArrayRecordFormatter );
        $this->addStage( $compiler );

        return $this;
    }
}
