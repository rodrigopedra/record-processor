<?php

namespace RodrigoPedra\RecordProcessor\Support\TransferObjects;

use RodrigoPedra\RecordProcessor\Contracts\Record;

final class FlushPayload
{
    private ?Record $record = null;
    private int $lineCount = 0;
    private int $recordCount = 0;
    private ?string $serializerClassName = null;
    protected mixed $output = null;

    public function hasRecord(): bool
    {
        return ! \is_null($this->record);
    }

    public function record(): ?Record
    {
        return $this->record;
    }

    public function withRecord(?Record $record): self
    {
        $this->record = $record;

        return $this;
    }

    public function lineCount(): int
    {
        return $this->lineCount;
    }

    public function withLineCount(int $lineCount): self
    {
        $this->lineCount = $lineCount;

        return $this;
    }

    public function recordCount(): int
    {
        return $this->recordCount;
    }

    public function withRecordCount($recordCount): self
    {
        $this->recordCount = $recordCount;

        return $this;
    }

    public function output(): mixed
    {
        return $this->output;
    }

    public function withOutput(mixed $output): self
    {
        $this->output = $output;

        return $this;
    }

    public function serializerClassName(): ?string
    {
        return $this->serializerClassName;
    }

    public function withSerializerClassName(string $className): self
    {
        $this->serializerClassName = $className;

        return $this;
    }
}
