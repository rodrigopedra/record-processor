<?php

namespace RodrigoPedra\RecordProcessor\Configurators\Serializers;

use RodrigoPedra\RecordProcessor\Serializers\ExcelFileSerializer;

/**
 * @property  \RodrigoPedra\RecordProcessor\Serializers\ExcelFileSerializer $serializer
 */
class ExcelFileSerializerConfigurator extends SerializerConfigurator
{
    protected ?\Closure $workbookConfigurator = null;

    protected ?\Closure $worksheetConfigurator = null;

    public function __construct(ExcelFileSerializer $serializer, bool $hasHeader = false, bool $hasTrailler = false)
    {
        parent::__construct($serializer, $hasHeader, $hasTrailler);
    }

    public function withWorkbookConfigurator(callable $workbookConfigurator): void
    {
        $this->workbookConfigurator = $workbookConfigurator(...);
    }

    public function withWorksheetConfigurator(callable $worksheetConfigurator): void
    {
        $this->worksheetConfigurator = $worksheetConfigurator(...);
    }

    public function workbookConfigurator(): ?\Closure
    {
        return $this->workbookConfigurator;
    }

    public function worksheetConfigurator(): ?\Closure
    {
        return $this->worksheetConfigurator;
    }
}
