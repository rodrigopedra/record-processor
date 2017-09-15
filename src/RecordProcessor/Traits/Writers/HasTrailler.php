<?php

namespace RodrigoPedra\RecordProcessor\Traits\Writers;

use InvalidArgumentException;
use RodrigoPedra\RecordProcessor\Exceptions\InvalidAddonException;
use RodrigoPedra\RecordProcessor\Helpers\Writers\WriterAddon;

trait HasTrailler
{
    /** @var WriterAddon|null */
    protected $trailler = null;

    public function getTrailler()
    {
        return $this->trailler;
    }

    public function setTrailler( $trailler )
    {
        try {
            $this->trailler = new WriterAddon( $trailler );
        } catch ( InvalidAddonException $ex ) {
            throw new InvalidArgumentException( 'Writer header should be an array or an callable' );
        }

        return $this;
    }
}
