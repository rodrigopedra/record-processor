<?php

namespace RodrigoPedra\RecordProcessor\Records;

use Illuminate\Support\Traits\ForwardsCalls;
use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\RecordAggregate;

class RecordKeyAggregate implements RecordAggregate
{
    use ForwardsCalls;

    protected Record $master;

    /** @var \RodrigoPedra\RecordProcessor\Contracts\Record[] */
    protected array $records = [];

    public function __construct(Record $record)
    {
        $this->master = $record;

        $this->addRecord($record);
    }

    public function key(): mixed
    {
        return $this->master->key();
    }

    public function field(string $field): mixed
    {
        return $this->master->field($field);
    }

    public function isValid(): bool
    {
        return $this->master->isValid();
    }

    public function master(): Record
    {
        return $this->master;
    }

    public function addRecord(Record $record): bool
    {
        if ($record->key() !== $this->key()) {
            return false;
        }

        if ($record->isValid()) {
            $this->records[] = $record;
        }

        return true;
    }

    public function records(): iterable
    {
        return $this->records;
    }

    public function toArray(): array
    {
        return [
            'master' => $this->master->toArray(),
            'records' => \array_map(fn (Record $record) => $record->toArray(), $this->records),
        ];
    }

    public function __get(string $name)
    {
        return $this->master->{$name};
    }

    public function __call(string $name, array $arguments)
    {
        return $this->forwardCallTo($this->master, $name, $arguments);
    }
}
