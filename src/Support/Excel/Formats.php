<?php

namespace RodrigoPedra\RecordProcessor\Support\Excel;

use PhpOffice\PhpSpreadsheet\Shared\Date;

class Formats
{
    protected function __construct() {}

    public static function currency(): string
    {
        return '_-[$R$-416]\ * #,##0.00_-;[Red]\-[$R$-416]\ * #,##0.00_-;_-[$R$-416]\ * "-"??_-;_-@_-';
    }

    public static function integer(): string
    {
        return '#,##0_ ;[Red]\-#,##0_ ;\-_ ';
    }

    public static function decimal(): string
    {
        return '#,##0.00_ ;[Red]\-#,##0.00_ ;\-_ ';
    }

    public static function percentage(): string
    {
        return '#,##0.00%_ ;[Red]\-#,##0,00%_ ;\-_ ';
    }

    public static function text(): string
    {
        return '@';
    }

    public static function date(): string
    {
        return 'dd/mm/yyyy';
    }

    public static function datetime(): string
    {
        return 'dd/mm/yyyy hh:mm:ss';
    }

    public static function general(): string
    {
        return 'General';
    }

    public static function formatExcelDatetime(?\DateTimeInterface $value = null): ?string
    {
        if (\is_null($value)) {
            return null;
        }

        return \number_format(Date::dateTimeToExcel($value), 12, '.', '');
    }
}
