<?php

namespace RodrigoPedra\RecordProcessor\Configurators\Readers;

use RodrigoPedra\RecordProcessor\Contracts\Reader;
use RodrigoPedra\RecordProcessor\Contracts\RecordParser;
use RodrigoPedra\RecordProcessor\RecordParsers\CallbackRecordParser;

class ReaderConfigurator
{
    protected ?RecordParser $recordParser = null;

    public function __construct(
        protected Reader $reader,
    ) {}

    public function hasRecordParser(): bool
    {
        return ! \is_null($this->recordParser);
    }

    public function recordParser(): RecordParser
    {
        return $this->recordParser ?? $this->reader->defaultRecordParser();
    }

    public function withRecordParser($recordParser): static
    {
        if (\is_callable($recordParser)) {
            $this->recordParser = new CallbackRecordParser($recordParser);

            return $this;
        }

        if (! ($recordParser instanceof RecordParser)) {
            throw new \InvalidArgumentException('Parser should implement ' . RecordParser::class);
        }

        $this->recordParser = $recordParser;

        return $this;
    }
}
