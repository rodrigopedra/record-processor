<?php

namespace RodrigoPedra\RecordProcessor\Writers;

use Exception;
use RuntimeException;

class PDOBufferedWriter extends PDOWriter
{
    const BUFFER_LIMIT = 1000;

    /** @var array */
    protected $buffer = [];

    public function close()
    {
        $this->flush();

        parent::close();
    }

    /**
     * @param  array $content
     *
     * @return void
     * @throws Exception
     */
    public function append( $content )
    {
        if (!is_array( $content )) {
            throw new RuntimeException( 'content for PDOBufferedWriter should be an array' );
        }

        $this->pushValues( $content );
    }

    public function pushValues( array $values )
    {
        $count = array_push( $this->buffer, $values );

        if ($count === static::BUFFER_LIMIT) {
            $this->flush();
        }
    }

    public function flush()
    {
        $count = count( $this->buffer );

        if ($count === 0) {
            return;
        }

        try {
            $data   = $this->flushData();
            $writer = $this->prepareWriter( $count );

            if (!$writer->execute( $data )) {
                throw new RuntimeException( 'Could not write PDO records' );
            }

            $this->incrementLineCount( $writer->rowCount() );
        } catch ( Exception $exception ) {
            if ($this->inTransaction) {
                $this->pdo->rollBack();
                $this->inTransaction = false;
            }

            throw $exception;
        } finally {
            $data = null;
        }
    }

    protected function prepareWriter( $count )
    {
        if ($count !== static::BUFFER_LIMIT) {
            $this->writer = null;
        }

        return parent::prepareWriter( $count );
    }

    protected function flushData()
    {
        $result = [];

        foreach ($this->buffer as $values) {
            $values = $this->prepareValuesForInsert( $values );

            array_push( $result, ...$values );
        }

        $this->buffer = [];

        return $result;
    }
}
