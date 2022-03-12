<?php

namespace RodrigoPedra\RecordProcessor;

use RodrigoPedra\RecordProcessor\Contracts\HaltsOnInvalid;
use RodrigoPedra\RecordProcessor\Contracts\Reader;
use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\RecordParser;

final class Parser extends \IteratorIterator
{
    private Reader $reader;
    private RecordParser $recordParser;
    private ?Record $current = null;

    public function __construct(Reader $reader)
    {
        parent::__construct($reader);

        $this->reader = $reader;
        $this->recordParser = $reader->configurator()->recordParser();
    }

    public function current(): Record
    {
        return $this->current = $this->recordParser->parseRecord($this->reader, parent::current());
    }

    public function valid(): bool
    {
        if (! parent::valid()) {
            return false;
        }

        if ($this->recordParser instanceof HaltsOnInvalid) {
            return $this->current?->isValid() ?? true;
        }

        return true;
    }

    public function open()
    {
        $this->reader->open();
    }

    public function close()
    {
        $this->reader->close();
    }

    public function lineCount(): int
    {
        return $this->reader->lineCount();
    }
}
