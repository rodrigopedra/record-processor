<?php

namespace RodrigoPedra\RecordProcessor\Serializers;

class PDOBufferedSeriealizer extends PDOSerializer
{
    protected const BUFFER_LIMIT = 100;

    protected array $buffer = [];

    public function close()
    {
        $this->flush();

        parent::close();
    }

    public function append($content)
    {
        if (! \is_array($content)) {
            throw new \InvalidArgumentException('content for PDOBufferedSerializer should be an array');
        }

        $this->pushValues($content);
    }

    public function pushValues(array $values)
    {
        $count = \array_push($this->buffer, $values);

        if ($count === static::BUFFER_LIMIT) {
            $this->flush();
        }
    }

    public function flush()
    {
        $count = \count($this->buffer);

        if ($count === 0) {
            return;
        }

        try {
            $data = $this->flushData();
            $statement = $this->prepareStatement($count);

            if (! $statement->execute($data)) {
                throw new \RuntimeException('Could not write PDO records');
            }

            $this->incrementLineCount($statement->rowCount());
        } catch (\Throwable $exception) {
            if ($this->inTransaction) {
                $this->pdo->rollBack();
                $this->inTransaction = false;
            }

            throw $exception;
        } finally {
            $data = null;
        }
    }

    protected function prepareStatement(int $count): ?\PDOStatement
    {
        if ($count !== static::BUFFER_LIMIT) {
            $this->statement = null;
        }

        return parent::prepareStatement($count);
    }

    protected function flushData(): array
    {
        $result = [];

        foreach ($this->buffer as $values) {
            $values = $this->prepareValuesForInsert($values);

            \array_push($result, ...$values);
        }

        $this->buffer = [];

        return $result;
    }
}
