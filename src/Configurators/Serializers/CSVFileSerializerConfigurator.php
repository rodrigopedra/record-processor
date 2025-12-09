<?php

namespace RodrigoPedra\RecordProcessor\Configurators\Serializers;

use League\Csv\Bom;
use RodrigoPedra\RecordProcessor\Serializers\CSVFileSerializer;
use RodrigoPedra\RecordProcessor\Support\EOL;

/**
 * @property  \RodrigoPedra\RecordProcessor\Serializers\CSVFileSerializer $serializer
 */
final class CSVFileSerializerConfigurator extends SerializerConfigurator
{
    public function __construct(CSVFileSerializer $serializer, bool $hasHeader = false, bool $hasTrailler = false)
    {
        parent::__construct($serializer, $hasHeader, $hasTrailler);
    }

    public function withOutputBOM(string $outputBOM): self
    {
        $this->serializer->withOutputBOM(Bom::tryFromSequence($outputBOM) ?? Bom::Utf8);

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

    public function withEndOfLine(EOL $endOfLine): self
    {
        $this->serializer->withEndOfLine($endOfLine);

        return $this;
    }
}
