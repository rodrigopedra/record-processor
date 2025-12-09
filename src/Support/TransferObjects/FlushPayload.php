<?php

namespace RodrigoPedra\RecordProcessor\Support\TransferObjects;

use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Records\NullRecord;

final class FlushPayload
{
    private Record $record;

    private int $lineCount = 0;

    private int $recordCount = 0;

    private ?string $serializerClassName = null;

    private mixed $output = null;

    public function __construct()
    {
        $this->record = NullRecord::get();
    }

    public function hasRecord(): bool
    {
        return ! ($this->record instanceof NullRecord);
    }

    public function record(): Record
    {
        return $this->record;
    }

    public function withRecord(?Record $record): self
    {
        $this->record = $record ?? NullRecord::get();

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
