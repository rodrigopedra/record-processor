<?php

namespace RodrigoPedra\RecordProcessor\Helpers;

use SplFileInfo;
use function RodrigoPedra\RecordProcessor\value_or_null;

class FileInfo extends SplFileInfo
{
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
}
