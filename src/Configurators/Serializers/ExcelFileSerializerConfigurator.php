<?php

namespace RodrigoPedra\RecordProcessor\Configurators\Serializers;

use RodrigoPedra\RecordProcessor\Serializers\ExcelFileSerializer;

/**
 * @property  \RodrigoPedra\RecordProcessor\Serializers\ExcelFileSerializer $serializer
 */
final class ExcelFileSerializerConfigurator extends SerializerConfigurator
{
    private ?\Closure $workbook = null;

    private ?\Closure $worksheet = null;

    public function __construct(ExcelFileSerializer $serializer, bool $hasHeader = false, bool $hasTrailler = false)
    {
        parent::__construct($serializer, $hasHeader, $hasTrailler);
    }

    public function withWorkbookConfigurator(callable $workbookConfigurator): void
    {
        $this->workbook = $workbookConfigurator(...);
    }

    public function withWorksheetConfigurator(callable $worksheetConfigurator): void
    {
        $this->worksheet = $worksheetConfigurator(...);
    }

    public function workbookConfigurator(): ?\Closure
    {
        return $this->workbook;
    }

    public function worksheetConfigurator(): ?\Closure
    {
        return $this->worksheet;
    }
}
