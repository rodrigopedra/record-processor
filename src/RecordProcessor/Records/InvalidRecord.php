<?php

namespace RodrigoPedra\RecordProcessor\Records;

use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\TextRecord;

class InvalidRecord implements Record, TextRecord
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

    public function toText()
    {
        return '';
    }

    public function getKey()
    {
        return '';
    }
}
