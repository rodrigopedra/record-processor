<?php

namespace RodrigoPedra\RecordProcessor\Helpers\Excel;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Based on LaravelExcel Excel writer
 *
 * @category   Laravel Excel
 * @version    1.0.0
 * @package    maatwebsite/excel
 * @copyright  Copyright (c) 2013 - 2014 Maatwebsite (http://www.maatwebsite.nl)
 * @author     Maatwebsite <info@maatwebsite.nl>
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 */
class CellWriter
{

    /**
     * Current $sheet
     *
     * @var Worksheet
     */
    public $sheet;

    /**
     * Selected cells
     *
     * @var string
     */
    public $cells;

    /**
     * Constructor
     *
     * @param string    $cells
     * @param Worksheet $sheet
     */
    public function __construct( $cells, Worksheet $sheet )
    {
        $this->cells = $cells;
        $this->sheet = $sheet;
    }

    /**
     * Set cell value
     *
     * @param [type] $value
     *
     * @return  CellWriter
     */
    public function setValue( $value )
    {
        // Only set cell value for single cells
        if (!str_contains( $this->cells, ':' )) {
            $this->sheet->setCellValue( $this->cells, $value );
        }

        return $this;
    }

    /**
     * Set cell url
     *
     * @param [type] $url
     *
     * @return  CellWriter
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function setUrl( $url )
    {
        // Only set cell value for single cells
        if (!str_contains( $this->cells, ':' )) {
            $this->sheet->getCell( $this->cells )->getHyperlink()->setUrl( $url );
        }

        return $this;
    }

    /**
     * Set the background
     *
     * @param string $color
     * @param string $type
     * @param string $colorType
     *
     * @return  CellWriter
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function setBackground( $color, $type = 'solid', $colorType = 'rgb' )
    {
        return $this->setColorStyle( 'fill', $color, $type, $colorType );
    }

    /**
     * Set the font color
     *
     * @param string $color
     * @param string $colorType
     *
     * @return  CellWriter
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function setFontColor( $color, $colorType = 'rgb' )
    {
        return $this->setColorStyle( 'font', $color, false, $colorType );
    }

    /**
     * Set the font
     *
     * @param $styles
     *
     * @return  CellWriter
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function setFont( $styles )
    {
        return $this->setStyle( 'font', $styles );
    }

    /**
     * Set font family
     *
     * @param string $family
     *
     * @return  CellWriter
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function setFontFamily( $family )
    {
        return $this->setStyle( 'font', [
            'name' => $family,
        ] );
    }

    /**
     * Set font size
     *
     * @param  string $size
     *
     * @return  CellWriter
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function setFontSize( $size )
    {
        return $this->setStyle( 'font', [
            'size' => $size,
        ] );
    }

    /**
     * Set font weight
     *
     * @param  boolean|string $bold
     *
     * @return  CellWriter
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function setFontWeight( $bold = true )
    {
        return $this->setStyle( 'font', [
            'bold' => ( $bold === 'bold' || $bold === true ),
        ] );
    }

    /**
     * Set border
     *
     * @param string      $top
     * @param bool|string $right
     * @param bool|string $bottom
     * @param bool|string $left
     *
     * @return  CellWriter
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function setBorder( $top = 'none', $right = 'none', $bottom = 'none', $left = 'none' )
    {
        // Set the border styles
        $styles = is_array( $top )
            ? $top
            : [
                'top'    => [
                    'style' => $top,
                ],
                'left'   => [
                    'style' => $left,
                ],
                'right'  => [
                    'style' => $right,
                ],
                'bottom' => [
                    'style' => $bottom,
                ],
            ];

        return $this->setStyle( 'borders', $styles );
    }

    /**
     * Set the text rotation
     *
     * @param $degrees
     *
     * @return  CellWriter
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function setTextRotation( $degrees )
    {
        $this->getCellStyle()->getAlignment()->setTextRotation( $degrees );

        return $this;
    }

    /**
     * Set the alignment
     *
     * @param string $alignment
     *
     * @return  CellWriter
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function setAlignment( $alignment )
    {
        return $this->setStyle( 'alignment', [
            'horizontal' => $alignment,
        ] );
    }

    /**
     * Set vertical alignment
     *
     * @param string $alignment
     *
     * @return  CellWriter
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function setValignment( $alignment )
    {
        return $this->setStyle( 'alignment', [
            'vertical' => $alignment,
        ] );
    }

    /**
     * Set the text indent
     *
     * @param integer $indent
     *
     * @return  CellWriter
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function setTextIndent( $indent )
    {
        $this->getCellStyle()->getAlignment()->setIndent( (int)$indent );

        return $this;
    }

    /**
     * Set the color style
     *
     * @param         $styleType
     * @param string  $color
     * @param boolean $type
     * @param string  $colorType
     *
     * @return  CellWriter
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    protected function setColorStyle( $styleType, $color, $type = false, $colorType = 'rgb' )
    {
        // Set the styles
        $styles = is_array( $color )
            ? $color
            : [
                'type'  => $type,
                'color' => [ $colorType => str_replace( '#', '', $color ) ],
            ];

        return $this->setStyle( $styleType, $styles );
    }

    /**
     * Set style
     *
     * @param              $styleType
     * @param array|string $styles
     *
     * @return  CellWriter
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    protected function setStyle( $styleType, $styles )
    {
        // Get the cell style
        $style = $this->getCellStyle();

        // Apply style from array
        $style->applyFromArray( [
            $styleType => $styles,
        ] );

        return $this;
    }

    /**
     * Get the cell style
     *
     * @return \PhpOffice\PhpSpreadsheet\Style\Style
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    protected function getCellStyle()
    {
        return $this->sheet->getStyle( $this->cells );
    }
}
