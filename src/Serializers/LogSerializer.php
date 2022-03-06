<?php

namespace RodrigoPedra\RecordProcessor\Serializers;

use Illuminate\Support\Arr;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use RodrigoPedra\RecordProcessor\Concerns\CountsLines;
use RodrigoPedra\RecordProcessor\Concerns\HasLogger;
use RodrigoPedra\RecordProcessor\Concerns\HasPrefix;
use RodrigoPedra\RecordProcessor\Concerns\NoOutput;
use RodrigoPedra\RecordProcessor\Configurators\Serializers\LogSerializerConfigurator;
use RodrigoPedra\RecordProcessor\Contracts\Serializer;
use RodrigoPedra\RecordProcessor\RecordSerializers\ArrayRecordSerializer;

class LogSerializer implements Serializer, LoggerAwareInterface
{
    use CountsLines;
    use HasLogger;
    use HasPrefix;

    protected string $level;
    protected LogSerializerConfigurator $configurator;

    public function __construct(LoggerInterface $logger)
    {
        $this->setLogger($logger);
        $this->withLevel(LogLevel::INFO);
        $this->configurator = new LogSerializerConfigurator($this, true, true);
    }

    public function withLevel(string $level): static
    {
        if (! \in_array($level, [
            LogLevel::EMERGENCY,
            LogLevel::ALERT,
            LogLevel::CRITICAL,
            LogLevel::ERROR,
            LogLevel::WARNING,
            LogLevel::NOTICE,
            LogLevel::INFO,
            LogLevel::DEBUG,
        ])) {
            throw new \InvalidArgumentException('Invalid log level. See Psr\\Log\\LogLevel class for available levels');
        }

        $this->level = $level;

        return $this;
    }

    public function open()
    {
        $this->lineCount = 0;
    }

    public function close()
    {
    }

    public function append($content)
    {
        $this->logger->log($this->level, $this->prefix(), Arr::wrap($content));

        $this->incrementLineCount();
    }

    public function output()
    {
        return null;
    }

    public function configurator(): LogSerializerConfigurator
    {
        return $this->configurator;
    }

    public function defaultRecordSerializer(): ArrayRecordSerializer
    {
        return new ArrayRecordSerializer();
    }
}
