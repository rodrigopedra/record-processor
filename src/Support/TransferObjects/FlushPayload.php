<?php

namespace RodrigoPedra\RecordProcessor\Support\TransferObjects;

use RodrigoPedra\RecordProcessor\Contracts\Record;

final class FlushPayload
{
    private ?Record $record = null;
    private int $lineCount = 0;
    private int $recordCount = 0;
    private ?string $serializerClassName = null;

    /** @var mixed */
    protected $output = null;

    public function hasRecord(): bool
    {
        return ! \is_null($this->record);
    }

    public function record(): ?Record
    {
        return $this->record;
    }

    public function withRecord(?Record $record)
    {
        $this->record = $record;
    }

    public function lineCount(): int
    {
        return $this->lineCount;
    }

    public function withLineCount(int $lineCount)
    {
        $this->lineCount = $lineCount;
    }

    public function recordCount(): int
    {
        return $this->recordCount;
    }

    public function withRecordCount($recordCount)
    {
        $this->recordCount = $recordCount;
    }

    public function output()
    {
        return $this->output;
    }

    public function withOutput($output)
    {
        $this->output = $output;
    }

    public function serializerClassName(): ?string
    {
        return $this->serializerClassName;
    }

    public function withSerializerClassName(string $className)
    {
        $this->serializerClassName = $className;
    }
}
