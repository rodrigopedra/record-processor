<?php

namespace RodrigoPedra\RecordProcessor\Concerns\Serializers;

use RodrigoPedra\RecordProcessor\Configurators\Serializers\SerializerAddon;

trait HasHeader
{
    protected ?SerializerAddon $header = null;

    public function header(): ?SerializerAddon
    {
        return $this->header;
    }

    public function withHeader(callable|array $header): static
    {
        $this->header = new SerializerAddon($header);

        return $this;
    }
}
