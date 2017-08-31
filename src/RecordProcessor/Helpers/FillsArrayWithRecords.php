<?php

namespace RodrigoPedra\RecordProcessor\Helpers;

use RodrigoPedra\RecordProcessor\Contracts\Record;

/**
 * Trait FillsArrayWithRecords
 *
 * Use with a \RodrigoPedra\RecordProcessor\Contracts\RecordAggregate implementation
 *
 * @package RodrigoPedra\RecordProcessor\Helpers
 */
trait FillsArrayWithRecords
{
    /**
     * @param  array  $results
     * @param  Record $record
     * @param  int    $offset
     *
     * @return int returns the record size
     */
    abstract protected function fillArrayWithSingleRecord( array &$results, Record $record, $offset );

    /**
     * @param  array $results
     * @param  int   $limit
     * @param  int   $offset
     *
     * @return void
     */
    protected function fillArrayWithRecords( array &$results, $limit, $offset )
    {
        $records = $this->getRecords();
        $length  = min( count( $records ), $limit );

        for ($index = 0; $index < $length; $index++) {
            /** @var Record $record */
            $record = $records[ $index ];

            $offset += $this->fillArrayWithSingleRecord( $results, $record, $offset );
        }
    }
}
