<?php

namespace RodrigoPedra\RecordProcessor\Reader;

use RodrigoPedra\RecordProcessor\Configurators\Readers\ReaderConfigurator;
use RodrigoPedra\RecordProcessor\Contracts\RecordParser;
use RodrigoPedra\RecordProcessor\RecordParsers\ArrayRecordParser;
use RodrigoPedra\RecordProcessor\Support\NewLines;

/**
 * @property  \SplFileObject $iterator
 */
class TextFileParser extends FileReader
{
    protected ReaderConfigurator $configurator;

    public function __construct($file)
    {
        parent::__construct($file);

        $this->configurator = new ReaderConfigurator($this);
    }

    public function open()
    {
        parent::open();

        $this->withInnerIterator($this->file);
    }

    public function current(): string
    {
        $content = $this->iteratorCurrent();

        return \rtrim($content, NewLines::WINDOWS_NEWLINE);
    }

    public function valid(): bool
    {
        return $this->iteratorValid() && ! $this->iterator->eof();
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
