<?php

namespace RodrigoPedra\RecordProcessor\Records;

use BadMethodCallException;
use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\RecordAggregate;

class RecordKeyAggregate implements RecordAggregate
{
    /** @var Record */
    protected $master;

    /** @var Record[] */
    protected $records = [];

    public function __construct( Record $record )
    {
        $this->master = $record;

        $this->pushRecord( $record );
    }

    public function get( $field, $default = null )
    {
        return $this->master->get( $field, $default );
    }

    public function set( $field, $value )
    {
        $this->master->set( $field, $value );
    }

    public function getKey()
    {
        return $this->master->getKey();
    }

    public function valid()
    {
        return $this->master->valid()
               && count( $this->records ) > 0;
    }

    public function pushRecord( Record $record )
    {
        if ($record->getKey() === $this->getKey()) {
            if ($record->valid()) {
                array_push( $this->records, $record );
            }

            return true;
        }

        return false;
    }

    public function getRecords()
    {
        return $this->records;
    }

    public function toArray()
    {
        return [
            'master'  => $this->master->toArray(),
            'records' => array_map( function ( Record $record ) { return $record->toArray(); }, $this->records )
        ];
    }

    public function __call( $method, $parameters )
    {
        if (method_exists( $this->master, $method )) {
            return $this->master->{$method}( ...$parameters );
        }

        $className = get_class( $this->master );

        throw new BadMethodCallException( "Call to undefined method {$className}::{$method}()" );
    }
}
