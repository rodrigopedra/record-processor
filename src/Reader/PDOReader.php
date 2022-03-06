<?php

namespace RodrigoPedra\RecordProcessor\Reader;

use RodrigoPedra\RecordProcessor\Concerns\CountsLines;
use RodrigoPedra\RecordProcessor\Configurators\Readers\ReaderConfigurator;
use RodrigoPedra\RecordProcessor\Contracts\Reader;
use RodrigoPedra\RecordProcessor\Contracts\RecordParser;
use RodrigoPedra\RecordProcessor\RecordParsers\ArrayRecordParser;

class PDOReader implements Reader
{
    use CountsLines;

    protected ?\PDO $pdo = null;
    protected ?\PDOStatement $statement = null;
    protected string $query;
    protected ?array $bindings = null;
    protected ?array $currentRecord = null;
    protected ReaderConfigurator $configurator;

    public function __construct(\PDO $pdo, $query)
    {
        $this->pdo = $pdo;
        $this->query = $query;
        $this->configurator = new ReaderConfigurator($this);

        if ($this->pdo->getAttribute(\PDO::ATTR_DRIVER_NAME) == 'mysql') {
            $this->pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
        }
    }

    public function withBindings(array $bindings): static
    {
        $this->bindings = $bindings;

        return $this;
    }

    public function open()
    {
        $this->lineCount = 0;

        if (\is_null($this->statement)) {
            $this->statement = $this->pdo->prepare($this->query, [
                \PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY,
            ]);

            $this->statement->setFetchMode(\PDO::FETCH_ASSOC);
        } else {
            $this->statement->closeCursor();
        }

        $this->currentRecord = null;
    }

    public function close()
    {
        $this->statement = null;
        $this->pdo = null;
        $this->bindings = null;
        $this->currentRecord = null;
    }

    public function current(): ?array
    {
        return $this->currentRecord;
    }

    public function next()
    {
        $this->currentRecord = $this->statement->fetch() ?: null;
    }

    public function key(): int
    {
        return $this->lineCount;
    }

    public function valid(): bool
    {
        $valid = ! \is_null($this->currentRecord);

        if ($valid) {
            $this->incrementLineCount();
        }

        return $valid;
    }

    public function rewind()
    {
        if (! \is_null($this->currentRecord)) {
            $this->statement->closeCursor();
            $this->currentRecord = null;
        }

        if ($this->statement->execute($this->bindings) === false) {
            return;
        }

        $this->currentRecord = $this->statement->fetch() ?: null;
    }

    public function getInnerIterator(): static
    {
        return $this;
    }

    public function configurator(): ReaderConfigurator
    {
        return $this->configurator;
    }

    public function defaultRecordParser(): RecordParser
    {
        return new ArrayRecordParser();
    }
}
