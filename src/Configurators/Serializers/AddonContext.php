<?php

namespace RodrigoPedra\RecordProcessor\Configurators\Serializers;

use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\Serializer;

class AddonContext
{
    public function __construct(
        protected readonly Serializer $serializer,
        protected readonly int $recordCount,
        protected readonly ?Record $firstRecord = null,
    ) {}

    public function append($content): static
    {
        $this->serializer->append($content);

        return $this;
    }

    public function lineCount(): int
    {
        return $this->serializer->lineCount();
    }

    public function recordCount(): int
    {
        return $this->recordCount;
    }

    public function firstRecord(): ?Record
    {
        return $this->firstRecord;
    }
}
