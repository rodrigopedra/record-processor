<?php

namespace RodrigoPedra\RecordProcessor\Concerns;

use League\Csv\Bom;
use RodrigoPedra\RecordProcessor\Support\EOL;

trait HasCSVControls
{
    protected string $delimiter = ',';

    protected string $enclosure = '"';

    protected string $escape = '\\';

    protected EOL $endOfLine = EOL::UNIX;

    protected Bom $outputBOM;

    public function delimiter(): string
    {
        return $this->delimiter;
    }

    public function enclosure(): string
    {
        return $this->enclosure;
    }

    public function escape(): string
    {
        return $this->escape;
    }

    public function endOfLine(): string
    {
        return $this->endOfLine->value;
    }

    public function outputBOM(): Bom
    {
        return $this->outputBOM;
    }

    public function withDelimiter(string $delimiter): static
    {
        if (! $this->isValidCsvControl($delimiter)) {
            throw new \InvalidArgumentException('The delimiter must be a single character');
        }

        $this->delimiter = $delimiter;

        return $this;
    }

    public function withEnclosure(string $enclosure): static
    {
        if (! $this->isValidCsvControl($enclosure)) {
            throw new \InvalidArgumentException('The enclosure must be a single character');
        }

        $this->enclosure = $enclosure;

        return $this;
    }

    public function withEscape(string $escape): static
    {
        if (! $this->isValidCsvControl($escape)) {
            throw new \InvalidArgumentException('The escape character must be a single character');
        }

        $this->escape = $escape;

        return $this;
    }

    public function withEndOfLine(EOL $endOfLine): static
    {
        $this->endOfLine = $endOfLine;

        return $this;
    }

    public function withOutputBOM(Bom $outputBOM): static
    {
        $this->outputBOM = $outputBOM;

        return $this;
    }

    protected function isValidCsvControl(string $control): bool
    {
        return \mb_strlen($control) === 1;
    }
}
