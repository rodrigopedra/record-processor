<?php

namespace RodrigoPedra\RecordProcessor\Readers;

use SplFileObject;
use RodrigoPedra\RecordProcessor\Helpers\FileInfo;
use RodrigoPedra\RecordProcessor\Contracts\Reader;
use RodrigoPedra\RecordProcessor\Traits\CountsLines;
use RodrigoPedra\RecordProcessor\Traits\Readers\HasInnerIterator;

abstract class FileReader implements Reader
{
    use CountsLines, HasInnerIterator {
        current as iteratorCurrent;
        valid as iteratorValid;
    }

    /** @var SplFileObject|null */
    protected $file = null;

    /** @var FileInfo|null */
    protected $fileInfo = null;

    public function __construct($file)
    {
        $this->file = FileInfo::createReadableFileObject($file, 'rb');
        $this->fileInfo = $this->file->getFileInfo(FileInfo::class);
    }

    public function open()
    {
        $this->lineCount = 0;
    }

    public function close()
    {
        $this->setInnerIterator(null);
    }
}
