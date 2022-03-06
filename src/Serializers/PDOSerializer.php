<?php

namespace RodrigoPedra\RecordProcessor\Serializers;

use RodrigoPedra\RecordProcessor\Concerns\CountsLines;
use RodrigoPedra\RecordProcessor\Concerns\NoOutput;
use RodrigoPedra\RecordProcessor\Configurators\Serializers\PDOSerializerConfigurator;
use RodrigoPedra\RecordProcessor\Contracts\Serializer;
use RodrigoPedra\RecordProcessor\RecordSerializers\ArrayRecordSerializer;

class PDOSerializer implements Serializer
{
    use CountsLines;

    protected ?\PDO $pdo = null;
    protected ?\PDOStatement $statement = null;
    protected string $tableName;
    protected int $columnCount;
    protected array $columns = [];
    protected string $valuesStatement;
    protected bool $usesTransaction = true;
    protected bool $inTransaction = false;
    protected ?bool $isAssociative = null;
    protected PDOSerializerConfigurator $configurator;

    public function __construct(\PDO $pdo, string $tableName, array $columns)
    {
        $this->columnCount = \count($columns);

        if ($this->columnCount < 1) {
            throw new \InvalidArgumentException('Columns array should contain at least one column');
        }

        $this->pdo = $pdo;
        $this->tableName = $tableName;
        $this->columns = $this->isAssociative($columns) ? \array_keys($columns) : $columns;
        $this->valuesStatement = $this->formatValuesString($this->columnCount);
        $this->configurator = new PDOSerializerConfigurator($this, false, false);
    }

    public function withUsesTransaction(bool $usesTransaction): static
    {
        $this->usesTransaction = $usesTransaction;

        return $this;
    }

    public function open()
    {
        $this->lineCount = 0;

        if ($this->usesTransaction === true) {
            $this->pdo->beginTransaction();
            $this->inTransaction = true;
        }
    }

    public function close()
    {
        if ($this->inTransaction) {
            $this->pdo->commit();
            $this->inTransaction = false;
        }

        $this->statement = null;
    }

    public function append($content)
    {
        if (! \is_array($content)) {
            throw new \InvalidArgumentException('Content for PDOSerializer should be an array');
        }

        try {
            $data = $this->prepareValuesForInsert($content);
            $statement = $this->prepareStatement(1);

            if (! $statement->execute($data)) {
                throw new \RuntimeException('Could not write PDO records');
            }

            $this->incrementLineCount($this->statement->rowCount());
        } catch (\Throwable $exception) {
            if ($this->inTransaction) {
                $this->pdo->rollBack();
                $this->inTransaction = false;
            }

            throw $exception;
        }
    }

    public function output()
    {
        return null;
    }

    protected function prepareStatement(int $count): ?\PDOStatement
    {
        if (! \is_null($this->statement)) {
            return $this->statement;
        }

        $query = $this->formatQueryStatement($count);

        $this->statement = $this->pdo->prepare($query);

        return $this->statement;
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

    protected function formatValuesString($valuesQuantity): string
    {
        return '(' . \implode(',', \array_fill(0, $valuesQuantity, '?')) . ')';
    }

    protected function sanitizeColumns(array $columns): string
    {
        $columns = \array_map(fn ($column) => \value($column), $columns);

        return '(' . \implode(',', $columns) . ')';
    }

    protected function prepareValuesForInsert(array $values): array
    {
        if (\count($values) !== $this->columnCount) {
            throw new \InvalidArgumentException('Record column count does not match PDOSerializer column definition');
        }

        if (\is_null($this->isAssociative)) {
            $this->isAssociative = $this->isAssociative($values);

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

    protected function isAssociative(array $array): bool
    {
        foreach ($array as $key => $value) {
            return \is_string($key);
        }

        return false;
    }
}
