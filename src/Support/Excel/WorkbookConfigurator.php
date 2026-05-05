<?php

namespace RodrigoPedra\RecordProcessor\Support\Excel;

use Illuminate\Support\Traits\ForwardsCalls;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 * @mixin  \PhpOffice\PhpSpreadsheet\Document\Properties
 */
class WorkbookConfigurator
{
    use ForwardsCalls;

    public function __construct(
        protected readonly Spreadsheet $workbook,
    ) {}

    public function workbook(): Spreadsheet
    {
        return $this->workbook;
    }

    public function __call(string $name, array $arguments)
    {
        return $this->forwardCallTo($this->workbook->getProperties(), $name, $arguments);
    }
}
