<?php

namespace RodrigoPedra\RecordProcessor\Support\Excel;

use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Style\Style;
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
    public Worksheet $sheet;
    public string $cells;

    public function __construct(string $cells, Worksheet $sheet)
    {
        $this->cells = $cells;
        $this->sheet = $sheet;
    }

    public function setValue($value): self
    {
        // Only set cell value for single cells
        if (! Str::contains($this->cells, ':')) {
            $this->sheet->setCellValue($this->cells, $value);
        }

        return $this;
    }

    public function setUrl(string $url): self
    {
        // Only set cell value for single cells
        if (! Str::contains($this->cells, ':')) {
            $this->sheet->getCell($this->cells)->getHyperlink()->setUrl($url);
        }

        return $this;
    }

    public function setBackground(string $color, string $type = 'solid', string $colorType = 'rgb'): self
    {
        return $this->setColorStyle('fill', $color, $type, $colorType);
    }

    public function setFontColor(string $color, string $colorType = 'rgb'): self
    {
        return $this->setColorStyle('font', $color, false, $colorType);
    }

    public function setFont($styles): self
    {
        return $this->setStyle('font', $styles);
    }

    public function setFontFamily(string $family): self
    {
        return $this->setStyle('font', ['name' => $family]);
    }

    public function setFontSize(string $size): self
    {
        return $this->setStyle('font', ['size' => $size]);
    }

    /**
     * @param  string|boolean  $bold
     * @return  CellWriter
     */
    public function setFontWeight($bold = true): self
    {
        return $this->setStyle('font', [
            'bold' => ($bold === 'bold' || $bold === true),
        ]);
    }

    /**
     * @param  bool|array  $top
     * @param  bool  $right
     * @param  bool  $bottom
     * @param  bool  $left
     * @return  CellWriter
     */
    public function setBorder($top = 'none', $right = 'none', $bottom = 'none', $left = 'none'): self
    {
        if (\is_array($top)) {
            $borders = $top;
        } else {
            $borders = [
                'top' => ['style' => $top],
                'left' => ['style' => $left],
                'right' => ['style' => $right],
                'bottom' => ['style' => $bottom],
            ];
        }

        return $this->setStyle('borders', $borders);
    }

    public function setTextRotation(int $degrees): self
    {
        $this->getCellStyle()->getAlignment()->setTextRotation($degrees);

        return $this;
    }

    public function setAlignment(string $alignment): self
    {
        return $this->setStyle('alignment', [
            'horizontal' => $alignment,
        ]);
    }

    public function setValignment(string $alignment): self
    {
        return $this->setStyle('alignment', [
            'vertical' => $alignment,
        ]);
    }

    public function setTextIndent(int $indent): self
    {
        $this->getCellStyle()->getAlignment()->setIndent($indent);

        return $this;
    }

    protected function setColorStyle($styleType, string $color, bool $type = false, string $colorType = 'rgb'): self
    {
        if (! \is_array($color)) {
            $color = [
                'type' => $type,
                'color' => [$colorType => \str_replace('#', '', $color)],
            ];
        }

        return $this->setStyle($styleType, $color);
    }

    protected function setStyle($styleType, $styles): self
    {
        $style = $this->getCellStyle();

        $style->applyFromArray([$styleType => $styles]);

        return $this;
    }

    protected function getCellStyle(): Style
    {
        return $this->sheet->getStyle($this->cells);
    }
}
