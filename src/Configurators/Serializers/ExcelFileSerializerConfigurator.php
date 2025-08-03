<?php

namespace RodrigoPedra\RecordProcessor\Configurators\Serializers;

use RodrigoPedra\RecordProcessor\Serializers\ExcelFileSerializer;

class ExcelFileSerializerConfigurator extends SerializerConfigurator
{
    /** @var  callable|null */
    protected $workbookConfigurator;

    /** @var  callable|null */
    protected $worksheetConfigurator;

    public function __construct(ExcelFileSerializer $serializer, bool $hasHeader = false, bool $hasTrailler = false)
    {
        parent::__construct($serializer, $hasHeader, $hasTrailler);
    }

    public function withWorkbookConfigurator(callable $workbookConfigurator)
    {
        $this->workbookConfigurator = $workbookConfigurator;
    }

    public function withWorksheetConfigurator(callable $worksheetConfigurator)
    {
        $this->worksheetConfigurator = $worksheetConfigurator;
    }

    public function workbookConfigurator(): ?callable
    {
        return $this->workbookConfigurator;
    }

    public function worksheetConfigurator(): ?callable
    {
        return $this->worksheetConfigurator;
    }
}
