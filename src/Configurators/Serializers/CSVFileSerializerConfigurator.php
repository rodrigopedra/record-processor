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

    public function withOutputBOM(string $outputBOM): self
    {
        $this->serializer->withOutputBOM($outputBOM);

        return $this;
    }

    public function withDelimiter(string $delimiter): self
    {
        $this->serializer->withDelimiter($delimiter);

        return $this;
    }

    public function withEnclosure(string $enclosure): self
    {
        $this->serializer->withEnclosure($enclosure);

        return $this;
    }

    public function withEscape(string $escape): self
    {
        $this->serializer->withEscape($escape);

        return $this;
    }

    public function withNewLine(string $newline): self
    {
        $this->serializer->withNewline($newline);

        return $this;
    }
}
