<?php

namespace RodrigoPedra\RecordProcessor\Configurators\Serializers;

use RodrigoPedra\RecordProcessor\Serializers\EchoSerializer;

/**
 * @property  \RodrigoPedra\RecordProcessor\Serializers\EchoSerializer $serializer
 */
class EchoSerializerConfigurator extends SerializerConfigurator
{
    public function __construct(EchoSerializer $serializer, bool $hasHeader = false, bool $hasTrailler = false)
    {
        parent::__construct($serializer, $hasHeader, $hasTrailler);
    }

    public function withPrefix(?string $prefix = null): static
    {
        $this->serializer->withPrefix($prefix);

        return $this;
    }
}
