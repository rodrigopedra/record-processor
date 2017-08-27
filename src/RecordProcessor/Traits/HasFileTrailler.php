<?php

namespace RodrigoPedra\RecordProcessor\Traits;

trait HasFileTrailler
{
    /** @var mixed */
    protected $trailler = null;

    public function getTrailler()
    {
        return $this->trailler;
    }

    public function setTrailler( $trailler )
    {
        $this->trailler = $trailler;

        return $this;
    }
}
