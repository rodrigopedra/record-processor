<?php

namespace RodrigoPedra\RecordProcessor\Configurators\Serializers;

use RodrigoPedra\RecordProcessor\Serializers\LogSerializer;

/**
 * @property  \RodrigoPedra\RecordProcessor\Serializers\LogSerializer $serializer
 */
final class LogSerializerConfigurator extends SerializerConfigurator
{
    public function __construct(LogSerializer $serializer, bool $hasHeader = false, bool $hasTrailler = false)
    {
        parent::__construct($serializer, $hasHeader, $hasTrailler);
    }

    public function withPrefix(?string $prefix = null): self
    {
        $this->serializer->withPrefix($prefix);

        return $this;
    }

    public function withLevel(string $level): self
    {
        $this->serializer->withLevel($level);

        return $this;
    }
}
