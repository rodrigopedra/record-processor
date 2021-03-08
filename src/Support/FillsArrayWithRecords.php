<?php

namespace RodrigoPedra\RecordProcessor\Support;

use RodrigoPedra\RecordProcessor\Contracts\Record;

/**
 * Trait FillsArrayWithRecords
 * Use with a \RodrigoPedra\RecordProcessor\Contracts\RecordAggregate implementation
 */
trait FillsArrayWithRecords
{
    abstract protected function fillArrayWithSingleRecord(array &$results, Record $record, int $offset): int;

    protected function fillArrayWithRecords(array &$results, array $records, int $limit, int $offset)
    {
        $length = \min(\count($records), $limit);

        $index = 0;

        foreach ($records as $record) {
            $offset += $this->fillArrayWithSingleRecord($results, $record, $offset);

            $index++;

            if ($index >= $length) {
                break;
            }
        }
    }
}
