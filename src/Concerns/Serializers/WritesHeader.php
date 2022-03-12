<?php

namespace RodrigoPedra\RecordProcessor\Concerns\Serializers;

use RodrigoPedra\RecordProcessor\Configurators\Serializers\SerializerAddon;
use RodrigoPedra\RecordProcessor\Contracts\Record;

trait WritesHeader
{
    abstract public function header(): ?SerializerAddon;

    protected function writeHeader(?Record $firstRecord = null): static
    {
        $this->header()?->handle($this->serializer, $this->recordCount(), $firstRecord);

        return $this;
    }
}
