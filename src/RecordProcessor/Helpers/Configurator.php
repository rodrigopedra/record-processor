<?php

namespace RodrigoPedra\RecordProcessor\Helpers;

use BadMethodCallException;
use RodrigoPedra\RecordProcessor\Contracts\Configurable;

class Configurator
{
    /** @var Configurable */
    protected $configurable;

    public function __construct( Configurable $configurable )
    {
        $this->configurable = $configurable;
    }

    public function __call( $method, $parameters )
    {
        if (in_array( $method, $this->configurable->getConfigurableMethods() )) {
            $this->configurable->{$method}( ...$parameters );

            return $this;
        }

        $className = get_class( $this->configurable );

        throw new BadMethodCallException( "Call to undefined method {$className}::{$method}()" );
    }
}
