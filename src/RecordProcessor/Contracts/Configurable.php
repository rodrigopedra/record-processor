<?php

namespace RodrigoPedra\RecordProcessor\Contracts;

interface Configurable
{
    /**
     * @return array
     */
    public function getConfigurableSetters();
}
