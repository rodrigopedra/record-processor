<?php

namespace RodrigoPedra\RecordProcessor\Helpers\Excel;

use DateTime;
use PHPExcel_Shared_Date;

class Formats
{
    protected function __construct() { }

    public static function currency()
    {
        return '_-[$R$-416]\ * #,##0.00_-;[Red]\-[$R$-416]\ * #,##0.00_-;_-[$R$-416]\ * "-"??_-;_-@_-';
    }

    public static function integer()
    {
        return '#,##0_ ;[Red]\-#,##0_ ;\-_ ';
    }

    public static function decimal()
    {
        return '#,##0.00_ ;[Red]\-#,##0.00_ ;\-_ ';
    }

    public static function percentage()
    {
        return '#,##0.00%_ ;[Red]\-#,##0,00%_ ;\-_ ';
    }

    public static function text()
    {
        return '@';
    }

    public static function date()
    {
        return 'dd/mm/yyyy';
    }

    public static function datetime()
    {
        return 'dd/mm/yyyy hh:mm:ss';
    }

    public static function general()
    {
        return 'General';
    }

    public static function formatExcelDatetime( DateTime $value = null )
    {
        if (empty( $value )) {
            return null;
        }

        return number_format( PHPExcel_Shared_Date::PHPToExcel( $value, true, 'America/Sao_Paulo' ), 12, '.', '' );
    }
}
