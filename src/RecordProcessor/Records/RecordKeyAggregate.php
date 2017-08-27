<?php

namespace RodrigoPedra\RecordProcessor\Records;

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

    public function getField( $field, $default = '' )
    {
        return $this->master->getField( $field, $default );
    }

    public function valid()
    {
        return $this->master->valid()
               && count( $this->records ) > 0;
    }

    public function toArray()
    {
        return [
            'master'  => $this->master->toArray(),
            'records' => array_map( function ( Record $record ) { return $record->toArray(); }, $this->records )
        ];
    }

    public function getKey()
    {
        return $this->master->getKey();
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
}
