<?php

namespace RodrigoPedra\RecordProcessor\Examples\RecordObjects;

use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\RecordAggregate;
use RodrigoPedra\RecordProcessor\Contracts\RecordAggregateFormatter;
use RodrigoPedra\RecordProcessor\Contracts\Writer;
use RodrigoPedra\RecordProcessor\Records\ArrayRecord;

class ExampleRecordAggregateFormatter implements RecordAggregateFormatter
{
    /** @var ExampleRecordFormatter */
    protected $recordFormatter;

    public function __construct()
    {
        $this->recordFormatter = new ExampleRecordFormatter;
    }

    /**
     * @param  Writer                 $writer
     * @param  RecordAggregate|Record $master
     *
     * @return bool
     */
    public function formatRecord( Writer $writer, Record $master )
    {
        if (!$master->valid()) {
            return false;
        }

        $children = $this->formatChildren( $writer, $master->getRecords() );
        $content  = [
            'name'  => $master->getKey(),
            'email' => $children,
        ];

        return $this->recordFormatter->formatRecord( $writer, new ArrayRecord( $content ) );
    }

    /**
     * Encode Record objects content to writer format
     *
     * @param  Writer   $writer
     * @param  Record[] $children
     *
     * @return bool
     */
    public function formatChildren( Writer $writer, array $children )
    {
        return implode( ', ', array_map( function ( Record $record ) {
            return $record->getField( 'email' );
        }, $children ) );
    }
}