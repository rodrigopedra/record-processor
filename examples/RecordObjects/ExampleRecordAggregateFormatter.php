<?php

namespace RodrigoPedra\RecordProcessor\Examples\RecordObjects;

use RuntimeException;
use RodrigoPedra\RecordProcessor\Contracts\Writer;
use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Records\SimpleRecord;
use RodrigoPedra\RecordProcessor\Contracts\RecordFormatter;
use RodrigoPedra\RecordProcessor\Contracts\RecordAggregate;

class ExampleRecordAggregateFormatter implements RecordFormatter
{
    /** @var ExampleRecordFormatter */
    protected $recordFormatter;

    public function __construct()
    {
        $this->recordFormatter = new ExampleRecordFormatter;
    }

    /**
     * @param  Writer  $writer
     * @param  RecordAggregate|Record  $master
     * @return bool
     */
    public function formatRecord(Writer $writer, Record $master)
    {
        if (! $master instanceof RecordAggregate) {
            throw new RuntimeException('Record for ExampleRecordAggregateFormatter should implement RecordAggregate interface');
        }

        if (! $master->valid()) {
            return false;
        }

        $children = $this->formatChildren($master->getRecords());
        $content = [
            'name' => $master->getKey(),
            'email' => $children,
        ];

        return $this->recordFormatter->formatRecord($writer, new SimpleRecord($content));
    }

    public function formatChildren(array $children)
    {
        return implode(', ', array_map(function (Record $record) {
            return $record->get('email');
        }, $children));
    }
}
