<?php

namespace RodrigoPedra\RecordProcessor\Serializers;

use Illuminate\Support\Arr;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use RodrigoPedra\RecordProcessor\Concerns\CountsLines;
use RodrigoPedra\RecordProcessor\Concerns\HasPrefix;
use RodrigoPedra\RecordProcessor\Configurators\Serializers\LogSerializerConfigurator;
use RodrigoPedra\RecordProcessor\Contracts\Serializer;
use RodrigoPedra\RecordProcessor\RecordSerializers\ArrayRecordSerializer;

class LogSerializer implements Serializer, LoggerAwareInterface
{
    use CountsLines;
    use HasPrefix;
    use LoggerAwareTrait;

    protected readonly LogSerializerConfigurator $configurator;

    protected string $level;

    public function __construct(LoggerInterface $logger)
    {
        $this->configurator = new LogSerializerConfigurator($this, true, true);
        $this->setLogger($logger);
        $this->withLevel(LogLevel::INFO);
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
        ], true)) {
            throw new \InvalidArgumentException('Invalid log level. See Psr\\Log\\LogLevel class for available levels');
        }

        $this->level = $level;

        return $this;
    }

    public function open(): void
    {
        $this->lineCount = 0;
    }

    public function close(): void {}

    public function append($content): void
    {
        $this->logger->log($this->level, $this->prefix(), Arr::wrap($content));

        $this->incrementLineCount();
    }

    public function output(): null
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
