<?php

namespace RodrigoPedra\RecordProcessor\Records;

use RodrigoPedra\RecordProcessor\Contracts\Record;

class InvalidRecord implements Record
{
    public function get( $field, $default = null )
    {
        return '';
    }

    public function set( $field, $value )
    {
        //
    }

    public function getKey()
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
}
