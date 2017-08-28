<?php

namespace RodrigoPedra\RecordProcessor\Records;

use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\TextRecord;
use function RodrigoPedra\RecordProcessor\value_or_null;

class ArrayRecord implements Record, TextRecord
{
    /** @var array */
    protected $values;

    /** @var string */
    protected $delimiter;

    public function __construct( array $values, $delimiter = ',' )
    {
        $this->values    = $values;
        $this->delimiter = $delimiter;
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

    public function toText()
    {
        return implode( $this->delimiter, $this->toArray() );
    }

    public function getKey()
    {
        return reset( $this->values ) ?: '';
    }
}
