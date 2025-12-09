<?php

namespace RodrigoPedra\RecordProcessor\Configurators\Serializers;

use RodrigoPedra\RecordProcessor\Serializers\TextFileSerializer;
use RodrigoPedra\RecordProcessor\Support\EOL;

/**
 * @property  \RodrigoPedra\RecordProcessor\Serializers\TextFileSerializer $serializer
 */
class TextFileSerializerConfigurator extends SerializerConfigurator
{
    public function __construct(TextFileSerializer $serializer, bool $hasHeader = false, bool $hasTrailler = false)
    {
        parent::__construct($serializer, $hasHeader, $hasTrailler);
    }

    public function withEndOfLine(EOL $endOf): static
    {
        $this->serializer->withEndOfLine($endOf);

        return $this;
    }
}
