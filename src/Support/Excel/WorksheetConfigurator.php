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

    public function __construct(
        protected Worksheet $worksheet,
    ) {
    }

    public function withColumnFormat(array $formats): static
    {
        foreach ($formats as $column => $format) {
            $this->worksheet
                ->getStyle($column)
                ->getNumberFormat()
                ->setFormatCode($format);
        }

        return $this;
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function freezeFirstRow(): static
    {
        $this->worksheet->freezePane('A2');

        return $this;
    }

    public function configureCells($range, callable $callback): static
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
