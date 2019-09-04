<?php

namespace RodrigoPedra\RecordProcessor\Helpers\Excel;

use BadMethodCallException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class WorkbookConfigurator
{
    /** @var Spreadsheet */
    protected $workbook;

    /** @var \PhpOffice\PhpSpreadsheet\Document\Properties */
    protected $properties;

    public function __construct(Spreadsheet $workbook)
    {
        $this->workbook = $workbook;
        $this->properties = $workbook->getProperties();
    }

    public function getWorkbook()
    {
        return $this->workbook;
    }

    public function __call($method, $parameters)
    {
        if (method_exists($this->properties, $method)) {
            return $this->properties->{$method}(...$parameters);
        }

        $className = get_class($this);

        throw new BadMethodCallException("Call to undefined method {$className}::{$method}()");
    }
}
