<?php

namespace RodrigoPedra\RecordProcessor\Serializers;

use RodrigoPedra\RecordProcessor\Concerns\CountsLines;
use RodrigoPedra\RecordProcessor\Configurators\Serializers\PDOSerializerConfigurator;
use RodrigoPedra\RecordProcessor\Contracts\Serializer;
use RodrigoPedra\RecordProcessor\RecordSerializers\ArrayRecordSerializer;

class PDOSerializer implements Serializer
{
    use CountsLines;

    protected readonly PDOSerializerConfigurator $configurator;

    protected array $columns;

    protected readonly int $columnCount;

    protected readonly string $valuesStatement;

    protected bool $usesTransaction = true;

    protected ?\PDOStatement $statement = null;

    protected bool $inTransaction = false;

    protected ?bool $isAssociative = null;

    public function __construct(
        protected readonly \PDO $pdo,
        protected readonly string $tableName,
        array $columns,
    ) {
        $this->columns = \array_is_list($columns) ? $columns : \array_keys($columns);
        $this->columnCount = \count($this->columns);

        if ($this->columnCount < 1) {
            throw new \InvalidArgumentException('Columns array should contain at least one column');
        }

        $this->configurator = new PDOSerializerConfigurator($this, false, false);
        $this->valuesStatement = $this->formatValuesString();
    }

    public function withUsesTransaction(bool $usesTransaction): static
    {
        $this->usesTransaction = $usesTransaction;

        return $this;
    }

    public function open(): void
    {
        $this->lineCount = 0;
        $this->beginTransaction();
    }

    public function close(): void
    {
        $this->commit();

        $this->statement = null;
        $this->isAssociative = null;
    }

    /**
     * @throws \Throwable
     */
    public function append($content): void
    {
        if (! \is_array($content)) {
            throw new \InvalidArgumentException('Content for PDOSerializer should be an array');
        }

        try {
            $data = $this->prepareValuesForInsert($content);
            $this->prepareStatement(1);

            if (! $this->statement->execute($data)) {
                throw new \RuntimeException('Could not write PDO records');
            }

            $this->incrementLineCount($this->statement->rowCount());
        } catch (\Throwable $exception) {
            $this->rollback();

            throw $exception;
        }
    }

    public function output(): null
    {
        return null;
    }

    protected function prepareStatement(int $count): void
    {
        if (\is_null($this->statement)) {
            $query = $this->formatQueryStatement($count);
            $this->statement = $this->pdo->prepare($query);
        }
    }

    protected function beginTransaction(): void
    {
        if ($this->usesTransaction === true) {
            $this->pdo->beginTransaction();
            $this->inTransaction = true;
        }
    }

    protected function commit(): void
    {
        if ($this->inTransaction) {
            $this->pdo->commit();
            $this->inTransaction = false;
        }
    }

    protected function rollback(): void
    {
        if ($this->inTransaction) {
            $this->pdo->rollBack();
            $this->inTransaction = false;
        }
    }

    protected function formatQueryStatement(int $count): string
    {
        $tokens = [
            'INSERT INTO',
            $this->tableName,
            $this->sanitizeColumns($this->columns),
            'VALUES',
            \implode(',', \array_fill(0, $count, $this->valuesStatement)),
        ];

        return \implode(' ', $tokens);
    }

    protected function formatValuesString(): string
    {
        return '(' . \implode(',', \array_fill(0, $this->columnCount, '?')) . ')';
    }

    protected function sanitizeColumns(array $columns): string
    {
        $columns = \array_map(\value(...), $columns);

        return '(' . \implode(',', $columns) . ')';
    }

    protected function prepareValuesForInsert(array $values): array
    {
        if (\count($values) !== $this->columnCount) {
            throw new \InvalidArgumentException('Record column count does not match PDOSerializer column definition');
        }

        if (\is_null($this->isAssociative)) {
            $this->isAssociative = ! \array_is_list($values);

            if ($this->isAssociative) {
                \sort($this->columns);
            }
        }

        if ($this->isAssociative) {
            \ksort($values);

            return \array_values($values);
        }

        return $values;
    }

    public function configurator(): PDOSerializerConfigurator
    {
        return $this->configurator;
    }

    public function defaultRecordSerializer(): ArrayRecordSerializer
    {
        return new ArrayRecordSerializer();
    }
}
