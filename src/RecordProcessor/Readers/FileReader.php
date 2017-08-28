<?php

namespace RodrigoPedra\RecordProcessor\Readers;

use RodrigoPedra\RecordProcessor\Contracts\Reader;
use RodrigoPedra\RecordProcessor\Helpers\FileInfo;
use RodrigoPedra\RecordProcessor\Traits\CountsLines;
use RodrigoPedra\RecordProcessor\Traits\ReaderInnerIterator;
use SplFileObject;

abstract class FileReader implements Reader
{
    use CountsLines, ReaderInnerIterator {
        current as iteratorCurrent;
        valid as iteratorValid;
    }

    /** @var SplFileObject|null */
    protected $file = null;

    /** @var FileInfo|null */
    protected $fileInfo = null;

    public function __construct( $file )
    {
        $this->file     = FileInfo::createReadableFileObject( $file, 'rb' );
        $this->fileInfo = $this->file->getFileInfo( FileInfo::class );
    }

    public function open()
    {
        $this->lineCount = 0;
        $this->file->rewind();
    }

    public function close()
    {
        $this->setInnerIterator( null );
    }
}
