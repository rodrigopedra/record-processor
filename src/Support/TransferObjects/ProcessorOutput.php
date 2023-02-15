<?php

namespace RodrigoPedra\RecordProcessor\Support\TransferObjects;

use RodrigoPedra\RecordProcessor\Support\FileInfo;

final class ProcessorOutput
{
    public function __construct(
        private readonly int $inputLineCount,
        private readonly int $inputRecordCount,
        private readonly int $outputLineCount,
        private readonly int $outputRecordCount,
        private mixed $output = null,
    ) {
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

    private function parseOutput(): void
    {
        if ($this->output instanceof \SplFileInfo) {
            $this->output = $this->output->getFileInfo(FileInfo::class);
        }
    }
}
