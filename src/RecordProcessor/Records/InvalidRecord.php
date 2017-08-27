<?php

namespace RodrigoPedra\RecordProcessor\Records;

use RodrigoPedra\RecordProcessor\Contracts\Record;

class InvalidRecord implements Record
{
    public function getField( $field, $default = '' )
    {
        return '';
    }

    public function valid()
    {
        return false;
    }

    public function toArray()
    {
        return [];
    }

    public function getKey()
    {
        return '';
    }
}
