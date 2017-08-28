<?php

namespace RodrigoPedra\RecordProcessor\Examples\RecordObjects;

use RodrigoPedra\RecordProcessor\Contracts\TextRecord;
use RodrigoPedra\RecordProcessor\Records\ArrayRecord;

class ExampleRecord extends ArrayRecord implements TextRecord
{
    public function getKey()
    {
        return $this->getField( 'name' );
    }

    public function valid()
    {
        return filter_var( $this->getField( 'email' ), FILTER_VALIDATE_EMAIL ) !== false;
    }

    /**
     * @return string
     */
    public function toText()
    {
        return implode( '|', $this->toArray() );
    }
}
