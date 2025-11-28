<?php

namespace RodrigoPedra\RecordProcessor\Reader;

use RodrigoPedra\RecordProcessor\Concerns\CountsLines;
use RodrigoPedra\RecordProcessor\Concerns\Readers\HasInnerIterator;
use RodrigoPedra\RecordProcessor\Configurators\Readers\ReaderConfigurator;
use RodrigoPedra\RecordProcessor\Contracts\Reader;
use RodrigoPedra\RecordProcessor\Contracts\RecordParser;
use RodrigoPedra\RecordProcessor\RecordParsers\ArrayRecordParser;
use RodrigoPedra\RecordProcessor\Support\FileInfo;

abstract class FileReader implements Reader
{
    use CountsLines;
    use HasInnerIterator {
        current as iteratorCurrent;
        valid as iteratorValid;
    }

    protected readonly \SplFileObject $file;

    public function __construct(
        protected readonly ReaderConfigurator $configurator,
        \SplFileInfo|string $file,
    ) {
        $this->file = FileInfo::createReadableFileObject($file);
    }

    public function open(): void
    {
        $this->lineCount = 0;
    }

    public function close(): void
    {
        $this->withInnerIterator(null);
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
