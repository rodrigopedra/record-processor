<?php

namespace RodrigoPedra\RecordProcessor\Traits\BuilderConcerns;

use Psr\Log\LogLevel;
use RodrigoPedra\RecordProcessor\Contracts\RecordAggregateFormatter;
use RodrigoPedra\RecordProcessor\Records\Formatter\ArrayRecordFormatter;
use RodrigoPedra\RecordProcessor\Stages\Compiler;
use RodrigoPedra\RecordProcessor\Stages\DownloadFileOutput;
use RodrigoPedra\RecordProcessor\Stages\RecordKeyAggregator;
use RodrigoPedra\RecordProcessor\Stages\ValidRecords;
use RodrigoPedra\RecordProcessor\Writers\EchoWriter;
use RodrigoPedra\RecordProcessor\Writers\LogWriter;

trait BuildsStages
{
    public function aggregateRecordsByKey( RecordAggregateFormatter $formatter = null )
    {
        $this->usingFormatter( $formatter );

        $this->addStage( new RecordKeyAggregator );

        return $this;
    }

    public function filterValidRecords()
    {
        $this->addStage( new ValidRecords );

        return $this;
    }

    public function downloadFileOutput( $outputFilename = '', $deleteFileAfterDownload = false )
    {
        $this->addStage( new DownloadFileOutput( $outputFilename, $deleteFileAfterDownload ) );

        return $this;
    }

    public function logRecords( $prefix = null )
    {
        $writer = new LogWriter( $this->getLogger() );
        $writer->setLevel( LogLevel::DEBUG );
        $writer->setPrefix( $prefix );

        $compiler = new Compiler( $writer, new ArrayRecordFormatter );

        $this->addStage( $compiler );

        return $this;
    }

    public function logInvalidRecords( $prefix = 'INVALID' )
    {
        $writer = new LogWriter( $this->getLogger() );
        $writer->setLevel( LogLevel::ERROR );
        $writer->setPrefix( $prefix );

        $compiler = new Compiler( $writer, new ArrayRecordFormatter( false ) );
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
