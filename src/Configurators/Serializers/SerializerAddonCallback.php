<?php

namespace RodrigoPedra\RecordProcessor\Configurators\Serializers;

use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\Serializer;

class SerializerAddonCallback
{
    protected Serializer $serializer;
    protected int $recordCount;
    protected ?Record $firstRecord;

    public function __construct(Serializer $serializer, int $recordCount, ?Record $firstRecord = null)
    {
        $this->serializer = $serializer;
        $this->recordCount = $recordCount;
        $this->firstRecord = $firstRecord;
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
