<?php

namespace RodrigoPedra\RecordProcessor\Examples\Loggers;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleOutputLogger implements LoggerInterface
{
    /** @var OutputInterface */
    private $output;

    public function __construct( OutputInterface $output )
    {
        $this->output = $output;
    }

    public function emergency( $message, array $context = [] )
    {
        $this->log( 'EMERGENCY', $message, $context );
    }

    public function alert( $message, array $context = [] )
    {
        $this->log( 'ALERT', $message, $context );
    }

    public function critical( $message, array $context = [] )
    {
        $this->log( 'CRITICAL', $message, $context );
    }

    public function error( $message, array $context = [] )
    {
        $this->log( 'ERROR', $message, $context );
    }

    public function warning( $message, array $context = [] )
    {
        $this->log( 'WARNING', $message, $context );
    }

    public function notice( $message, array $context = [] )
    {
        $this->log( 'NOTICE', $message, $context );
    }

    public function info( $message, array $context = [] )
    {
        $this->log( 'INFO', $message, $context );
    }

    public function debug( $message, array $context = [] )
    {
        $this->log( 'DEBUG', $message, $context );
    }

    public function log( $level, $message, array $context = [] )
    {
        $this->output->writeln( strtoupper( $level ) . ': ' . $message );

        if (count( $context )) {
            $this->output->writeln( var_export( $context, true ) );
        }

        $this->output->writeln( '' );
    }
}
