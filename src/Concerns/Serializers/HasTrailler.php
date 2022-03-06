<?php

namespace RodrigoPedra\RecordProcessor\Concerns\Serializers;

use RodrigoPedra\RecordProcessor\Configurators\Serializers\SerializerAddon;
use RodrigoPedra\RecordProcessor\Exceptions\InvalidAddonException;

trait HasTrailler
{
    protected ?SerializerAddon $trailler = null;

    public function trailler(): ?SerializerAddon
    {
        return $this->trailler;
    }

    public function withTrailler(SerializerAddon|callable|array|null $trailler): static
    {
        if (\is_null($trailler)) {
            $this->trailler = null;

            return $this;
        }

        if ($trailler instanceof SerializerAddon) {
            $this->trailler = $trailler;

            return $this;
        }

        try {
            $this->trailler = new SerializerAddon($trailler);
        } catch (InvalidAddonException) {
            throw new \InvalidArgumentException('Trailler should be an array or a callable');
        }

        return $this;
    }
}
