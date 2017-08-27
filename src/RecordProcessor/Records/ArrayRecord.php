<?php

namespace RodrigoPedra\RecordProcessor\Records;

use RodrigoPedra\RecordProcessor\Contracts\Record;
use function RodrigoPedra\RecordProcessor\value_or_null;

class ArrayRecord implements Record
{
    /** @var array */
    protected $values;

    public function __construct( array $values )
    {
        $this->values = $values;
    }

    public function getField( $field, $default = '' )
    {
        $value = array_get( $this->values, $field, $default );

        return value_or_null( $value ) ?: $default;
    }

    public function valid()
    {
        return count( $this->values ) > 0;
    }

    public function toArray()
    {
        return $this->values;
    }

    public function getKey()
    {
        return reset( $this->values ) ?: '';
    }
}
