<?php

namespace RodrigoPedra\RecordProcessor;

use RodrigoPedra\RecordProcessor\Concerns\CountsLines;
use RodrigoPedra\RecordProcessor\Concerns\CountsRecords;
use RodrigoPedra\RecordProcessor\Contracts\HaltsOnInvalid;
use RodrigoPedra\RecordProcessor\Contracts\Reader;
use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\RecordParser;

final class Parser implements \IteratorAggregate
{
    use CountsLines;
    use CountsRecords;

    private RecordParser $recordParser;

    public function __construct(
        private readonly Reader $reader,
    ) {
        $this->recordParser = $reader->configurator()->recordParser();
    }

    public function getIterator(): \Generator
    {
        foreach ($this->reader as $line) {
            $this->incrementLineCount();

            $records = $this->recordParser->parseRecord($this->reader, $line);

            if ($records instanceof Record) {
                $records = [$records];
            }

            /** @var  \RodrigoPedra\RecordProcessor\Contracts\Record $record */
            foreach ($records as $record) {
                if ($this->shouldHalt($record)) {
                    return;
                }

                $this->incrementRecordCount($this->countRecord($record));

                yield $record;
            }
        }
    }

    public function open(): void
    {
        $this->reader->open();
    }

    public function close(): void
    {
        $this->reader->close();
    }

    private function shouldHalt(Record $record): bool
    {
        if ($this->recordParser instanceof HaltsOnInvalid) {
            return ! $record->isValid();
        }

        return false;
    }

    private function countRecord(Record $record): int
    {
        if ($record instanceof \Countable) {
            return $record->count();
        }

        return \intval($record->isValid());
    }
}
