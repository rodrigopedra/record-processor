<?php

namespace RodrigoPedra\RecordProcessor;

use RodrigoPedra\RecordProcessor\Contracts\Reader;
use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\RecordParser;

class Parser extends \IteratorIterator
{
    protected Reader $reader;
    protected RecordParser $recordParser;

    public function __construct(Reader $reader)
    {
        parent::__construct($reader);

        $this->reader = $reader;
        $this->recordParser = $reader->configurator()->recordParser();
    }

    public function current(): Record
    {
        return $this->recordParser->parseRecord($this->reader, parent::current());
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
