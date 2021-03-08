<?php

namespace RodrigoPedra\RecordProcessor\Reader;

use RodrigoPedra\RecordProcessor\Concerns\CountsLines;
use RodrigoPedra\RecordProcessor\Concerns\Readers\HasInnerIterator;
use RodrigoPedra\RecordProcessor\Contracts\Reader;
use RodrigoPedra\RecordProcessor\Support\FileInfo;

abstract class FileReader implements Reader
{
    use CountsLines;
    use HasInnerIterator {
        current as iteratorCurrent;
        valid as iteratorValid;
    }

    protected ?\SplFileObject $file = null;
    protected FileInfo $fileInfo;

    public function __construct($file)
    {
        $this->file = FileInfo::createReadableFileObject($file);
        $this->fileInfo = $this->file->getFileInfo(FileInfo::class);
    }

    public function open()
    {
        $this->lineCount = 0;
    }

    public function close()
    {
        $this->withInnerIterator(null);
    }
}
