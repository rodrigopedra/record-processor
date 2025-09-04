<?php

namespace RodrigoPedra\RecordProcessor\Serializers;

class PDOBufferedSeriealizer extends PDOSerializer
{
    protected const BUFFER_LIMIT = 100;

    protected array $buffer = [];

    /**
     * @throws \Throwable
     */
    public function close(): void
    {
        $this->flush();

        parent::close();
    }

    public function append($content): void
    {
        if (! \is_array($content)) {
            throw new \InvalidArgumentException('content for PDOBufferedSerializer should be an array');
        }

        $this->pushValues($content);
    }

    /**
     * @throws \Throwable
     */
    public function pushValues(array $values): void
    {
        $count = \array_push($this->buffer, $values);

        if ($count === static::BUFFER_LIMIT) {
            $this->flush();
        }
    }

    /**
     * @throws \Throwable
     */
    public function flush(): void
    {
        $count = \count($this->buffer);

        if ($count === 0) {
            return;
        }

        try {
            $data = $this->flushData();
            $this->prepareStatement($count);

            if (! $this->statement->execute($data)) {
                throw new \RuntimeException('Could not write PDO records');
            }

            $this->incrementLineCount($this->statement->rowCount() ?? 0);
        } catch (\Throwable $exception) {
            $this->rollback();

            throw $exception;
        } finally {
            $data = null;
        }
    }

    protected function prepareStatement(int $count): void
    {
        if ($count !== static::BUFFER_LIMIT) {
            $this->statement = null;
        }

        parent::prepareStatement($count);
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
