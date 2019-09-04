<?php

namespace RodrigoPedra\RecordProcessor\Traits;

use function RodrigoPedra\RecordProcessor\value_or_null;

trait HasPrefix
{
    /** @var  string */
    protected $prefix;

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param  string  $prefix
     * @return void
     */
    public function setPrefix($prefix)
    {
        $this->prefix = value_or_null($prefix);
    }
}
