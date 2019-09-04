<?php

namespace RodrigoPedra\RecordProcessor\Writers;

use Psr\Log\LogLevel;
use Illuminate\Support\Arr;
use Psr\Log\LoggerInterface;
use InvalidArgumentException;
use Psr\Log\LoggerAwareInterface;
use RodrigoPedra\RecordProcessor\Traits\NoOutput;
use RodrigoPedra\RecordProcessor\Traits\HasLogger;
use RodrigoPedra\RecordProcessor\Traits\HasPrefix;
use RodrigoPedra\RecordProcessor\Traits\CountsLines;
use RodrigoPedra\RecordProcessor\Contracts\ConfigurableWriter;
use RodrigoPedra\RecordProcessor\Helpers\Writers\WriterConfigurator;

class LogWriter implements ConfigurableWriter, LoggerAwareInterface
{
    use CountsLines, HasLogger, HasPrefix, NoOutput;

    /** @var string */
    protected $level;

    public function __construct(LoggerInterface $logger)
    {
        $this->setLogger($logger);

        // default values
        $this->setLevel(LogLevel::INFO);
    }

    public function setLevel($level)
    {
        if (! in_array($level, [
            LogLevel::EMERGENCY,
            LogLevel::ALERT,
            LogLevel::CRITICAL,
            LogLevel::ERROR,
            LogLevel::WARNING,
            LogLevel::NOTICE,
            LogLevel::INFO,
            LogLevel::DEBUG,
        ])) {
            throw new InvalidArgumentException('Invalid log level. See Psr\\Log\\LogLevel class for available levels');
        }

        $this->level = $level;
    }

    public function open()
    {
        $this->lineCount = 0;
    }

    public function close()
    {
        //
    }

    public function append($content)
    {
        $this->logger->log($this->level, $this->getPrefix(), Arr::wrap($content));

        $this->incrementLineCount();
    }

    public function getConfigurableMethods()
    {
        return [
            'setLevel',
            'setPrefix',
        ];
    }

    public function createConfigurator()
    {
        return new WriterConfigurator($this, true, true);
    }
}
