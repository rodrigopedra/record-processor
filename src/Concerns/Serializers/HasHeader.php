<?php

namespace RodrigoPedra\RecordProcessor\Concerns\Serializers;

use RodrigoPedra\RecordProcessor\Configurators\Serializers\SerializerAddon;
use RodrigoPedra\RecordProcessor\Exceptions\InvalidAddonException;

trait HasHeader
{
    protected ?SerializerAddon $header = null;

    public function header(): ?SerializerAddon
    {
        return $this->header;
    }

    public function withHeader(SerializerAddon|callable|array|null $header): static
    {
        if (\is_null($header)) {
            $this->header = null;

            return $this;
        }

        if ($header instanceof SerializerAddon) {
            $this->header = $header;

            return $this;
        }

        try {
            $this->header = new SerializerAddon($header);
        } catch (InvalidAddonException) {
            throw new \InvalidArgumentException('Serializer header should be an array or a callable');
        }

        return $this;
    }
}
