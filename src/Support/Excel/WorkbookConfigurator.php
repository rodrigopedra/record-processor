<?php

namespace RodrigoPedra\RecordProcessor\Support\Excel;

use Illuminate\Support\Traits\ForwardsCalls;
use PhpOffice\PhpSpreadsheet\Document\Properties;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 * @mixin  \PhpOffice\PhpSpreadsheet\Document\Properties
 */
class WorkbookConfigurator
{
    use ForwardsCalls;

    protected Properties $properties;

    public function __construct(
        protected Spreadsheet $workbook,
    ) {
        $this->properties = $workbook->getProperties();
    }

    public function workbook(): Spreadsheet
    {
        return $this->workbook;
    }

    public function __call(string $name, array $arguments)
    {
        return $this->forwardCallTo($this->properties, $name, $arguments);
    }
}
