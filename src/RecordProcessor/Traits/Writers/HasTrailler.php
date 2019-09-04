<?php

namespace RodrigoPedra\RecordProcessor\Traits\Writers;

use InvalidArgumentException;
use RodrigoPedra\RecordProcessor\Helpers\Writers\WriterAddon;
use RodrigoPedra\RecordProcessor\Exceptions\InvalidAddonException;

trait HasTrailler
{
    /** @var WriterAddon|null */
    protected $trailler = null;

    public function getTrailler()
    {
        return $this->trailler;
    }

    public function setTrailler($trailler)
    {
        if (is_null($trailler)) {
            return $this;
        }

        try {
            $this->trailler = new WriterAddon($trailler);
        } catch (InvalidAddonException $ex) {
            throw new InvalidArgumentException('Writer header should be an array or a callable');
        }

        return $this;
    }
}
