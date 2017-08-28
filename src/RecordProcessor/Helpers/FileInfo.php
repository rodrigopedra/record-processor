<?php

namespace RodrigoPedra\RecordProcessor\Helpers;

use InvalidArgumentException;
use SplFileInfo;
use SplFileObject;
use SplTempFileObject;
use function RodrigoPedra\RecordProcessor\value_or_null;

class FileInfo extends SplFileInfo
{
    const INPUT_STREAM          = 'php://input';
    const OUTPUT_STREAM         = 'php://output';
    const TEMP_FILE             = 'php://temp';
    const TEMP_FILE_MEMORY_SIZE = 4194304; // 4MB

    public function getExtension()
    {
        return strtolower( parent::getExtension() );
    }

    public function getFileInfo( $className = null )
    {
        $className = value_or_null( $className );
        $className = $className ?: self::class;

        return parent::getFileInfo( $className );
    }

    public function isTempFile()
    {
        if ($this instanceof SplTempFileObject) {
            return true;
        }

        return substr( $this->getPathname(), 0, 10 ) === self::TEMP_FILE;
    }

    public function guessMimeType()
    {
        $mimeMap = [
            'csv'  => 'text/csv',
            'txt'  => 'text/plain',
            'htm'  => 'text/html',
            'html' => 'text/html',
            'json' => 'application/json',
            'xls'  => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];

        $extension = $this->getExtension();

        if (isset( $mimeMap[ $extension ] )) {
            return $mimeMap[ $extension ];
        }

        return mime_content_type( $this->getBasename() );
    }

    public function isCSV()
    {
        return $this->getExtension() === 'csv';
    }

    public function getBasenameWithoutExtension()
    {
        $extension = $this->getExtension();
        $extension = $extension ? '.' . $extension : $extension;

        return $this->getBasename( $extension );
    }

    public function getBasenameWithExtension( $extension = null )
    {
        return implode( '.', array_filter( [
            $this->getBasenameWithoutExtension(),
            value_or_null( $extension ),
        ] ) );
    }

    public static function createTempFileObject()
    {
        return new SplTempFileObject( self::TEMP_FILE_MEMORY_SIZE );
    }

    public static function createFileObject( $file, $mode )
    {
        if ($file === static::TEMP_FILE) {
            return static::createTempFileObject();
        }

        if (is_string( $file )) {
            $fileInfo = new static( $file );

            return $fileInfo->isTempFile() ? FileInfo::createTempFileObject() : $fileInfo->openFile( $mode );
        }

        if (!$file instanceof SplFileObject) {
            throw new InvalidArgumentException( 'File should be a path to a file or a SplFileObject' );
        }

        /** @var FileInfo $fileInfo */
        $fileInfo = $file->getFileInfo( static::class );

        if ($fileInfo->isTempFile()) {
            return $file;
        }

        $file = null;

        return $fileInfo->openFile( $mode );
    }

    public static function createWritableFileObject( $file, $mode = 'wb' )
    {
        $file = static::createFileObject( $file, $mode );

        /** @var FileInfo $fileInfo */
        $fileInfo = $file->getFileInfo( static::class );

        if ($fileInfo->isTempFile()) {
            $file->ftruncate( 0 );

            return $file;
        }

        if ($fileInfo->getPathname() === static::OUTPUT_STREAM) {
            return $file;
        }

        if (!$fileInfo->isWritable()) {
            $fileName = $fileInfo->getPathname();

            throw new InvalidArgumentException( "File {$fileName} is not writable" );
        }

        return $file;
    }

    public static function createReadableFileObject( $file, $mode = 'rb' )
    {
        $file = static::createFileObject( $file, $mode );

        /** @var FileInfo $fileInfo */
        $fileInfo = $file->getFileInfo( static::class );

        if ($fileInfo->isTempFile() || !$fileInfo->isReadable()) {
            $fileName = $fileInfo->getPathname();

            throw new InvalidArgumentException( "File {$fileName} is not readable" );
        }

        $file->rewind();

        return $file;
    }
}
