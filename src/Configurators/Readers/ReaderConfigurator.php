<?php

namespace RodrigoPedra\RecordProcessor\Configurators\Readers;

use RodrigoPedra\RecordProcessor\Contracts\Reader;
use RodrigoPedra\RecordProcessor\Contracts\RecordParser;
use RodrigoPedra\RecordProcessor\RecordParsers\CallbackRecordParser;

class ReaderConfigurator
{
    protected Reader $reader;
    protected ?RecordParser $recordParser = null;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function hasRecordParser(): bool
    {
        return ! \is_null($this->recordParser);
    }

    public function recordParser(): RecordParser
    {
        return $this->recordParser ?? $this->reader->defaultRecordParser();
    }

    public function withRecordParser($recordParser): self
    {
        if (\is_callable($recordParser)) {
            $this->recordParser = new CallbackRecordParser($recordParser);

            return $this;
        }

        if (! (\is_object($recordParser) && $recordParser instanceof RecordParser)) {
            throw new \InvalidArgumentException('Parser should implement ' . RecordParser::class);
        }

        $this->recordParser = $recordParser;

        return $this;
    }
}
