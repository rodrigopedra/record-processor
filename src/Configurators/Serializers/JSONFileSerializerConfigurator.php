<?php

namespace RodrigoPedra\RecordProcessor\Configurators\Serializers;

use RodrigoPedra\RecordProcessor\Serializers\JSONFileSerializer;

/**
 * @property  \RodrigoPedra\RecordProcessor\Serializers\JSONFileSerializer $serializer
 */
final class JSONFileSerializerConfigurator extends SerializerConfigurator
{
    public function __construct(JSONFileSerializer $serializer, bool $hasHeader = false, bool $hasTrailler = false)
    {
        parent::__construct($serializer, $hasHeader, $hasTrailler);
    }

    public function withEncodeOptions(int $encodeOptions): self
    {
        $this->serializer->withEncodeOptions($encodeOptions);

        return $this;
    }
}
