<?php

namespace RodrigoPedra\RecordProcessor\Configurators\Serializers;

use RodrigoPedra\RecordProcessor\Serializers\HTMLTableSerializer;

/**
 * @property  \RodrigoPedra\RecordProcessor\Serializers\HTMLTableSerializer $serializer
 */
final class HTMLTableSerializerConfigurator extends SerializerConfigurator
{
    public function __construct(HTMLTableSerializer $serializer, bool $hasHeader = false, bool $hasTrailler = false)
    {
        parent::__construct($serializer, $hasHeader, $hasTrailler);
    }

    public function writeOutputToFile(string $fileName): self
    {
        $this->serializer->writeOutputToFile($fileName);

        return $this;
    }

    public function withTableClassAttribute(string $tableClassAttribute): self
    {
        $this->serializer->withTableClassAttribute($tableClassAttribute);

        return $this;
    }

    public function withTableIdAttribute(string $tableIdAttribute): self
    {
        $this->serializer->withTableIdAttribute($tableIdAttribute);

        return $this;
    }
}
