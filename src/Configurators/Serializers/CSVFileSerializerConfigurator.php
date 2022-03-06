<?php

namespace RodrigoPedra\RecordProcessor\Configurators\Serializers;

use RodrigoPedra\RecordProcessor\Serializers\CSVFileSerializer;

/**
 * @property  \RodrigoPedra\RecordProcessor\Serializers\CSVFileSerializer $serializer
 */
class CSVFileSerializerConfigurator extends SerializerConfigurator
{
    public function __construct(CSVFileSerializer $serializer, bool $hasHeader = false, bool $hasTrailler = false)
    {
        parent::__construct($serializer, $hasHeader, $hasTrailler);
    }

    public function withOutputBOM(string $outputBOM): static
    {
        $this->serializer->withOutputBOM($outputBOM);

        return $this;
    }

    public function withDelimiter(string $delimiter): static
    {
        $this->serializer->withDelimiter($delimiter);

        return $this;
    }

    public function withEnclosure(string $enclosure): static
    {
        $this->serializer->withEnclosure($enclosure);

        return $this;
    }

    public function withEscape(string $escape): static
    {
        $this->serializer->withEscape($escape);

        return $this;
    }

    public function withNewLine(string $newline): static
    {
        $this->serializer->withNewline($newline);

        return $this;
    }
}
