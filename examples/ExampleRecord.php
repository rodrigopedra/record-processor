<?php

namespace RodrigoPedra\RecordProcessor\Examples;

use RodrigoPedra\RecordProcessor\Records\ArrayRecord;

class ExampleRecord extends ArrayRecord
{
    public function getKey()
    {
        return $this->getField( 'name' );
    }

    public function valid()
    {
        return filter_var( $this->getField( 'email' ), FILTER_VALIDATE_EMAIL ) !== false;
    }
}
