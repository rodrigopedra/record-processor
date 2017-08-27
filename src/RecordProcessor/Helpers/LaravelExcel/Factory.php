<?php

namespace RodrigoPedra\RecordProcessor\Helpers\LaravelExcel;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Classes\FormatIdentifier;
use Maatwebsite\Excel\Classes\PHPExcel;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Readers\LaravelExcelReader;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

class Factory
{
    /** @var Excel|null */
    protected static $instance = null;

    /**
     * @return Excel
     */
    public static function getExcel()
    {
        if (is_null( self::$instance )) {
            self::$instance = self::makeInstance();
        }

        return self::$instance;
    }

    /**
     * @return Excel
     */
    protected static function makeInstance()
    {
        $filesystem       = new Filesystem;
        $formatIdentifier = new FormatIdentifier( $filesystem );
        $reader           = self::makeReader( $filesystem, $formatIdentifier );
        $writer           = self::makeWriter( $filesystem, $formatIdentifier );

        $phpExcel = new PHPExcel;
        $phpExcel->setDefaultProperties();

        return new Excel( $phpExcel, $reader, $writer );
    }

    protected static function makeReader( Filesystem $filesystem, FormatIdentifier $formatIdentifier )
    {
        return new LaravelExcelReader(
            $filesystem,
            $formatIdentifier,
            new NullDispatcher
        );
    }

    protected static function makeWriter( Filesystem $filesystem, FormatIdentifier $formatIdentifier )
    {
        return new LaravelExcelWriter(
            new Response,
            $filesystem,
            $formatIdentifier
        );
    }
}
