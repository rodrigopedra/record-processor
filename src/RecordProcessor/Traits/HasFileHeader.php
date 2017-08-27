<?php

namespace RodrigoPedra\RecordProcessor\Traits;

trait HasFileHeader
{
    /** @var mixed */
    protected $header = null;

    public function getHeader()
    {
        return $this->header;
    }

    public function setHeader( $header )
    {
        $this->header = $header;

        return $this;
    }
}
