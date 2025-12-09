<?php

namespace RodrigoPedra\RecordProcessor\Configurators\Serializers;

use RodrigoPedra\RecordProcessor\Serializers\PDOSerializer;

/**
 * @property  \RodrigoPedra\RecordProcessor\Serializers\PDOSerializer $serializer
 */
final class PDOSerializerConfigurator extends SerializerConfigurator
{
    public function __construct(PDOSerializer $serializer, bool $hasHeader = false, bool $hasTrailler = false)
    {
        parent::__construct($serializer, $hasHeader, $hasTrailler);
    }

    public function useTransaction(bool $usesTransaction = true): self
    {
        $this->serializer->withUsesTransaction($usesTransaction);

        return $this;
    }
}
