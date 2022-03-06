<?php

namespace RodrigoPedra\RecordProcessor\Configurators\Serializers;

use RodrigoPedra\RecordProcessor\Serializers\PDOSerializer;

/**
 * @property  \RodrigoPedra\RecordProcessor\Serializers\PDOSerializer $serializer
 */
class PDOSerializerConfigurator extends SerializerConfigurator
{
    public function __construct(PDOSerializer $serializer, bool $hasHeader = false, bool $hasTrailler = false)
    {
        parent::__construct($serializer, $hasHeader, $hasTrailler);
    }

    public function withUsesTransaction(bool $usesTransaction): static
    {
        $this->serializer->withUsesTransaction($usesTransaction);

        return $this;
    }
}
