<?php

namespace RodrigoPedra\RecordProcessor\Support\Excel;

use Illuminate\Support\Traits\ForwardsCalls;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * @mixin \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
 */
class WorksheetConfigurator
{
    use ForwardsCalls;

    protected Worksheet $worksheet;

    public function __construct(Worksheet $worksheet)
    {
        $this->worksheet = $worksheet;
    }

    public function withColumnFormat(array $formats): self
    {
        foreach ($formats as $column => $format) {
            $this->worksheet
                ->getStyle($column)
                ->getNumberFormat()
                ->setFormatCode($format);
        }

        return $this;
    }

    public function freezeFirstRow(): self
    {
        $this->worksheet->freezePane('A2');

        return $this;
    }

    public function configureCells($range, callable $callback): self
    {
        $callback(new CellWriter($range, $this->worksheet));

        return $this;
    }

    public function worksheet(): Worksheet
    {
        return $this->worksheet;
    }

    public function __call(string $name, array $arguments)
    {
        return $this->forwardCallTo($this->worksheet, $name, $arguments);
    }
}
