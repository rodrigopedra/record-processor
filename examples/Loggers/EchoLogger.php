<?php

namespace RodrigoPedra\RecordProcessor\Examples\Loggers;

use Psr\Log\LoggerInterface;

class EchoLogger implements LoggerInterface
{
    public function emergency($message, array $context = []): void
    {
        $this->log('EMERGENCY', $message, $context);
    }

    public function alert($message, array $context = []): void
    {
        $this->log('ALERT', $message, $context);
    }

    public function critical($message, array $context = []): void
    {
        $this->log('CRITICAL', $message, $context);
    }

    public function error($message, array $context = []): void
    {
        $this->log('ERROR', $message, $context);
    }

    public function warning($message, array $context = []): void
    {
        $this->log('WARNING', $message, $context);
    }

    public function notice($message, array $context = []): void
    {
        $this->log('NOTICE', $message, $context);
    }

    public function info($message, array $context = []): void
    {
        $this->log('INFO', $message, $context);
    }

    public function debug($message, array $context = []): void
    {
        $this->log('DEBUG', $message, $context);
    }

    public function log($level, $message, array $context = []): void
    {
        echo \strtoupper($level), \PHP_EOL;
        echo $message, ': ', \PHP_EOL;

        if (\count($context)) {
            echo \var_export($context, true), \PHP_EOL;
        }

        echo \PHP_EOL;
    }
}
