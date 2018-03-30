<?php

namespace RodrigoPedra\RecordProcessor\Helpers\Excel;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WorksheetConfigurator
{
    /** @var Worksheet */
    protected $worksheet;

    public function __construct( Worksheet $worksheet )
    {
        $this->worksheet = $worksheet;
    }

    public function setTitle( $title )
    {
        $this->worksheet->setTitle( $title );

        return $this;
    }

    public function setColumnFormat( array $formats )
    {
        // Loop through the columns
        foreach ($formats as $column => $format) {
            // Change the format for a specific cell or range
            $this->worksheet
                ->getStyle( $column )
                ->getNumberFormat()
                ->setFormatCode( $format );
        }

        return $this;
    }

    public function freezeFirstRow()
    {
        $this->worksheet->freezePane( 'A2' );

        return $this;
    }

    public function cells( $range, callable $callback )
    {
        $cells = new CellWriter( $range, $this->worksheet );

        if (is_callable( $callback )) {
            call_user_func( $callback, $cells );
        }

        return $this;
    }

    public function getStyle( $range )
    {
        return $this->worksheet->getStyle( $range );
    }

    public function getWorksheet()
    {
        return $this->worksheet;
    }
}
