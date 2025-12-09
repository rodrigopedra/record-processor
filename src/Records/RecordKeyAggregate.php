<?php

namespace RodrigoPedra\RecordProcessor\Records;

use Illuminate\Support\Traits\ForwardsCalls;
use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\RecordAggregate;

class RecordKeyAggregate implements RecordAggregate
{
    use ForwardsCalls;

    /** @var \RodrigoPedra\RecordProcessor\Contracts\Record[] */
    protected array $records = [];

    public function __construct(
        protected readonly Record $master,
    ) {
        $this->addRecord($master);
    }

    public function key(): mixed
    {
        return $this->master->key();
    }

    public function field(string $field, $default = null)
    {
        return $this->master->field($field, $default);
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
            'records' => \array_map(static fn (Record $record) => $record->toArray(), $this->records),
        ];
    }

    public function count(): int
    {
        return \count($this->records);
    }

    public function __get(string $name)
    {
        return $this->master->{$name};
    }

    public function __set(string $name, mixed $value): void
    {
        throw new \BadMethodCallException('Cannot set properties on RecordKeyAggregate. Use the master record instead.');
    }

    public function __isset(string $name): bool
    {
        return isset($this->master->{$name});
    }

    public function __unset(string $name): void
    {
        throw new \BadMethodCallException('Cannot unset properties on RecordKeyAggregate. Use the master record instead.');
    }

    public function __call(string $name, array $arguments)
    {
        return $this->forwardCallTo($this->master, $name, $arguments);
    }
}
