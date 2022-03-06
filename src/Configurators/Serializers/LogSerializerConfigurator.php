<?php

namespace RodrigoPedra\RecordProcessor\Configurators\Serializers;

use RodrigoPedra\RecordProcessor\Serializers\LogSerializer;

/**
 * @property  \RodrigoPedra\RecordProcessor\Serializers\LogSerializer $serializer
 */
class LogSerializerConfigurator extends SerializerConfigurator
{
    public function __construct(LogSerializer $serializer, bool $hasHeader = false, bool $hasTrailler = false)
    {
        parent::__construct($serializer, $hasHeader, $hasTrailler);
    }

    public function withPrefix(?string $prefix = null): static
    {
        $this->serializer->withPrefix($prefix);

        return $this;
    }

    public function withLevel(string $level): static
    {
        $this->serializer->withLevel($level);

        return $this;
    }
}
