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

    protected Serializer $serializer;
    protected bool $hasHeader;
    protected bool $hasTrailler;
    protected ?RecordSerializer $recordSerializer = null;

    public function __construct(Serializer $serializer, bool $hasHeader = false, bool $hasTrailler = false)
    {
        $this->serializer = $serializer;
        $this->hasHeader = $hasHeader;
        $this->hasTrailler = $hasTrailler;
    }

    public function hasRecordSerializer(): bool
    {
        return ! \is_null($this->recordSerializer);
    }

    public function recordSerializer(): RecordSerializer
    {
        return $this->recordSerializer ?? $this->serializer->defaultRecordSerializer();
    }

    public function withRecordSerializer($recordSerializer): self
    {
        if (\is_callable($recordSerializer)) {
            $this->recordSerializer = new CallbackRecordSerializer($recordSerializer);

            return $this;
        }

        if (! (\is_object($recordSerializer) && $recordSerializer instanceof RecordSerializer)) {
            throw new \InvalidArgumentException('Serializer should implement ' . RecordSerializer::class);
        }

        $this->recordSerializer = $recordSerializer;

        return $this;
    }

    public function withHeader($header): self
    {
        if (! $this->hasHeader) {
            $className = \get_class($this->serializer);

            throw new \RuntimeException($className . ' does not accept a header');
        }

        $this->baseWithHeader($header);

        return $this;
    }

    public function withTrailler($trailler): self
    {
        if (! $this->hasTrailler) {
            $className = \get_class($this->serializer);

            throw new \RuntimeException($className . ' does not accept a trailler');
        }

        $this->baseWithTrailler($trailler);

        return $this;
    }
}
