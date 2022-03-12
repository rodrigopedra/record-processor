<?php

namespace RodrigoPedra\RecordProcessor\Concerns\Serializers;

use RodrigoPedra\RecordProcessor\Configurators\Serializers\SerializerAddon;

trait WritesTrailler
{
    abstract public function trailler(): ?SerializerAddon;

    protected function writeTrailler(): static
    {
        $this->trailler()?->handle($this->serializer, $this->recordCount());

        return $this;
    }
}
