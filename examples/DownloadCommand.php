<?php

namespace RodrigoPedra\RecordProcessor\Examples;

use RodrigoPedra\RecordProcessor\Examples\Loggers\ConsoleOutputLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class DownloadCommand extends Command
{
    protected function configure()
    {
        $this->setName( 'download' );

        $this->setDescription( 'Start a server to showcase download usage' );
    }

    protected function execute( InputInterface $input, OutputInterface $output )
    {
        $logger = new ConsoleOutputLogger( $output );

        $assetsDirectory = __DIR__ . DIRECTORY_SEPARATOR . 'download';
        $command         = "php -S localhost:8080 -t {$assetsDirectory}";

        $process = new Process( $command );

        $logger->info( 'Navigate in your browser to http://localhost:8080' );
        $logger->info( 'Type CTRL+C to exit' );

        $process->run( function ( $type, $buffer ) use ( $logger ) {
            $logger->info( $buffer );
        } );

        $logger->info( $process->getOutput() );
    }
}
