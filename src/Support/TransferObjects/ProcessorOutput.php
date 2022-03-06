<?php

namespace RodrigoPedra\RecordProcessor\Support\TransferObjects;

use RodrigoPedra\RecordProcessor\Support\FileInfo;

final class ProcessorOutput
{
    private int $inputLineCount;
    private int $inputRecordCount;
    private int $outputLineCount;
    private int $outputRecordCount;
    protected mixed $output = null;

    public function __construct(
        int $inputLineCount,
        int $inputRecordCount,
        int $outputLineCount,
        int $outputRecordCount,
        mixed $output
    ) {
        $this->inputLineCount = $inputLineCount;
        $this->inputRecordCount = $inputRecordCount;
        $this->outputLineCount = $outputLineCount;
        $this->outputRecordCount = $outputRecordCount;
        $this->output = $output;

        $this->parseOutput();
    }

    public function inputLineCount(): int
    {
        return $this->inputLineCount;
    }

    public function inputRecordCount(): int
    {
        return $this->inputRecordCount;
    }

    public function outputLineCount(): int
    {
        return $this->outputLineCount;
    }

    public function outputRecordCount(): int
    {
        return $this->outputRecordCount;
    }

    public function hasOutput(): bool
    {
        return ! \is_null($this->output);
    }

    public function output(): mixed
    {
        return $this->output;
    }

    protected function parseOutput()
    {
        if ($this->output instanceof \SplFileInfo) {
            $this->output = $this->output->getFileInfo(FileInfo::class);
        }
    }
}
