<?php

namespace RodrigoPedra\RecordProcessor\Writers;

use RodrigoPedra\RecordProcessor\Contracts\Writer;
use RodrigoPedra\RecordProcessor\Helpers\FileInfo;
use RodrigoPedra\RecordProcessor\Traits\CountsLines;
use SplFileObject;

abstract class FileWriter implements Writer
{
    use CountsLines;

    /** @var SplFileObject|null */
    protected $file = null;

    /** @var FileInfo|null */
    protected $fileInfo = null;

    public function __construct( $file = null )
    {
        $file = is_null( $file ) ? FileInfo::TEMP_FILE : $file;

        $this->file     = FileInfo::createWritableFileObject( $file, 'wb' );
        $this->fileInfo = $this->file->getFileInfo( FileInfo::class );
    }

    public function open()
    {
        $this->lineCount = 0;
        $this->file->ftruncate( 0 );
    }

    public function close()
    {
        //
    }

    /**
     * @return mixed
     */
    public function output()
    {
        return FileInfo::createReadableFileObject( $this->file, 'rb' );
    }
}
