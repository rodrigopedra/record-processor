<?php

namespace RodrigoPedra\RecordProcessor\Contracts;

use RodrigoPedra\RecordProcessor\Helpers\WriterConfigurator;

interface ConfigurableReader extends Configurable, Reader
{
    /**
     * @return WriterConfigurator
     */
    public function createConfigurator();
}
