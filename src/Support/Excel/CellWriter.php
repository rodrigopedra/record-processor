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
final class CellWriter
{
    public function __construct(
        public string $cells,
        public Worksheet $sheet,
    ) {}

    public function setValue($value): self
    {
        // Only set cell value for single cells
        if (! Str::contains($this->cells, ':')) {
            $this->sheet->setCellValue($this->cells, $value);
        }

        return $this;
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
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

    public function setFontWeight(bool|string $bold = true): self
    {
        return $this->setStyle('font', [
            'bold' => ($bold === 'bold' || $bold === true),
        ]);
    }

    public function setBorder(
        string|array $top = 'none',
        string $right = 'none',
        string $bottom = 'none',
        string $left = 'none',
    ): self {
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

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
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

    protected function setColorStyle(
        string $styleType,
        array|string $color,
        bool $type = false,
        string $colorType = 'rgb',
    ): self {
        if (! \is_array($color)) {
            $color = [
                'type' => $type,
                'color' => [$colorType => \str_replace('#', '', $color)],
            ];
        }

        return $this->setStyle($styleType, $color);
    }

    protected function setStyle(string $styleType, array $styles): self
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
