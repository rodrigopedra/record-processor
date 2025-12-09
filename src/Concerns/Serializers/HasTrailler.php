<?php

namespace RodrigoPedra\RecordProcessor\Concerns\Serializers;

use RodrigoPedra\RecordProcessor\Configurators\Serializers\SerializerAddon;

trait HasTrailler
{
    protected ?SerializerAddon $trailler = null;

    public function trailler(): ?SerializerAddon
    {
        return $this->trailler;
    }

    public function withTrailler(callable|array $trailler): static
    {
        $this->trailler = new SerializerAddon($trailler);

        return $this;
    }
}
