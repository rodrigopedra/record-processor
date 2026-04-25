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

    protected function fillArrayWithRecords(array &$results, iterable $records, int $limit, int $offset): void
    {
        $index = 0;

        foreach ($records as $record) {
            $offset += $this->fillArrayWithSingleRecord($results, $record, $offset);

            $index++;

            if ($index >= $limit) {
                break;
            }
        }
    }
}
