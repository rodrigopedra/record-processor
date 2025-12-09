<?php

namespace RodrigoPedra\RecordProcessor\Reader;

use RodrigoPedra\RecordProcessor\Configurators\Readers\ReaderConfigurator;
use RodrigoPedra\RecordProcessor\Support\EOL;

/**
 * @property  \SplFileObject $iterator
 */
class TextFileParser extends FileReader
{
    public function __construct(\SplFileInfo|string $file)
    {
        parent::__construct(
            configurator: new ReaderConfigurator($this),
            file: $file,
        );
    }

    public function open(): void
    {
        parent::open();

        $this->withInnerIterator($this->file);
    }

    public function current(): string
    {
        $content = $this->iteratorCurrent();

        return \rtrim($content, EOL::WINDOWS->value);
    }

    public function valid(): bool
    {
        return $this->iteratorValid() && ! $this->iterator->eof();
    }
}
