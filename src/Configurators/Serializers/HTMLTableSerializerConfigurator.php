<?php

namespace RodrigoPedra\RecordProcessor\Configurators\Serializers;

use RodrigoPedra\RecordProcessor\Serializers\HTMLTableSerializer;

/**
 * @property  \RodrigoPedra\RecordProcessor\Serializers\HTMLTableSerializer $serializer
 */
class HTMLTableSerializerConfigurator extends SerializerConfigurator
{
    public function __construct(HTMLTableSerializer $serializer, bool $hasHeader = false, bool $hasTrailler = false)
    {
        parent::__construct($serializer, $hasHeader, $hasTrailler);
    }

    public function writeOutputToFile(string $fileName): static
    {
        $this->serializer->writeOutputToFile($fileName);

        return $this;
    }

    public function withTableClassAttribute(string $tableClassAttribute): static
    {
        $this->serializer->withTableClassAttribute($tableClassAttribute);

        return $this;
    }

    public function withTableIdAttribute(string $tableIdAttribute): static
    {
        $this->serializer->withTableIdAttribute($tableIdAttribute);

        return $this;
    }
}
