<?php

namespace RodrigoPedra\RecordProcessor\Reader;

use RodrigoPedra\RecordProcessor\Concerns\CountsLines;
use RodrigoPedra\RecordProcessor\Concerns\Readers\HasInnerIterator;
use RodrigoPedra\RecordProcessor\Configurators\Readers\ReaderConfigurator;
use RodrigoPedra\RecordProcessor\Contracts\Reader;
use RodrigoPedra\RecordProcessor\Contracts\RecordParser;
use RodrigoPedra\RecordProcessor\RecordParsers\ArrayRecordParser;

class IteratorReader implements Reader
{
    use CountsLines;
    use HasInnerIterator;

    protected ReaderConfigurator $configurator;

    public function __construct(\Iterator $iterator)
    {
        $this->withInnerIterator($iterator);

        $this->configurator = new ReaderConfigurator($this);
    }

    public function open()
    {
        $this->lineCount = 0;

        $this->iterator->rewind();
    }

    public function close()
    {
    }

    public function configurator(): ReaderConfigurator
    {
        return $this->configurator;
    }

    public function defaultRecordParser(): RecordParser
    {
        return new ArrayRecordParser();
    }
}
