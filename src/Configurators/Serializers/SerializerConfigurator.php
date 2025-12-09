<?php

namespace RodrigoPedra\RecordProcessor\Configurators\Serializers;

use RodrigoPedra\RecordProcessor\Concerns\Serializers\HasHeader;
use RodrigoPedra\RecordProcessor\Concerns\Serializers\HasTrailler;
use RodrigoPedra\RecordProcessor\Contracts\RecordSerializer;
use RodrigoPedra\RecordProcessor\Contracts\Serializer;
use RodrigoPedra\RecordProcessor\RecordSerializers\CallbackRecordSerializer;

class SerializerConfigurator
{
    use HasHeader {
        withHeader as baseWithHeader;
    }
    use HasTrailler {
        withTrailler as baseWithTrailler;
    }

    protected ?RecordSerializer $recordSerializer = null;

    public function __construct(
        protected readonly Serializer $serializer,
        protected bool $hasHeader = false,
        protected bool $hasTrailler = false,
    ) {}

    public function hasRecordSerializer(): bool
    {
        return ! \is_null($this->recordSerializer);
    }

    public function recordSerializer(): RecordSerializer
    {
        return $this->recordSerializer ?? $this->serializer->defaultRecordSerializer();
    }

    public function withRecordSerializer($recordSerializer): static
    {
        if (\is_callable($recordSerializer)) {
            $this->recordSerializer = new CallbackRecordSerializer($recordSerializer);

            return $this;
        }

        if (! ($recordSerializer instanceof RecordSerializer)) {
            throw new \InvalidArgumentException('Serializer should implement ' . RecordSerializer::class);
        }

        $this->recordSerializer = $recordSerializer;

        return $this;
    }

    public function withHeader(SerializerAddon|callable|array|null $header): static
    {
        if (! $this->hasHeader) {
            throw new \RuntimeException($this->serializer::class . ' does not accept a header');
        }

        $this->baseWithHeader($header);

        return $this;
    }

    public function withTrailler(SerializerAddon|callable|array|null $trailler): static
    {
        if (! $this->hasTrailler) {
            throw new \RuntimeException($this->serializer::class . ' does not accept a trailler');
        }

        $this->baseWithTrailler($trailler);

        return $this;
    }
}
