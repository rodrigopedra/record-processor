<?php

namespace RodrigoPedra\RecordProcessor\Configurators\Serializers;

use RodrigoPedra\RecordProcessor\Serializers\TextFileSerializer;

/**
 * @property  \RodrigoPedra\RecordProcessor\Serializers\TextFileSerializer $serializer
 */
class TextFileSerializerConfigurator extends SerializerConfigurator
{
    public function __construct(TextFileSerializer $serializer, bool $hasHeader = false, bool $hasTrailler = false)
    {
        parent::__construct($serializer, $hasHeader, $hasTrailler);
    }

    public function withNewLine(string $newLine): static
    {
        $this->serializer->withNewLine($newLine);

        return $this;
    }
}
