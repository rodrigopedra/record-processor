<?php

namespace RodrigoPedra\RecordProcessor\Configurators\Serializers;

use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\Serializer;

class SerializerAddonCallback
{
    public function __construct(
        protected Serializer $serializer,
        protected int $recordCount,
        protected ?Record $firstRecord = null,
    ) {
    }

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
