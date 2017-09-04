<?php

namespace RodrigoPedra\RecordProcessor\Contracts;

use RodrigoPedra\RecordProcessor\Helpers\Configurator;

interface ConfigurableReader extends Configurable, Reader
{
    /**
     * @return Configurator
     */
    public function createConfigurator();
}
