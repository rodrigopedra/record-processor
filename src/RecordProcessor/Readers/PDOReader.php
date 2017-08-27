<?php

namespace RodrigoPedra\RecordProcessor\Readers;

use PDO;
use PDOStatement;
use RodrigoPedra\RecordProcessor\Contracts\Reader;
use RodrigoPedra\RecordProcessor\Traits\CountsLines;

class PDOReader implements Reader
{
    use CountsLines;

    /** @var PDO */
    protected $pdo = null;

    /** @var PDOStatement */
    protected $reader = null;

    /** @var string */
    protected $query;

    /** @var array */
    protected $queryParameters = [];

    /** @var array */
    protected $currentRecord = false;

    public function __construct( PDO $pdo, $query )
    {
        $this->pdo   = $pdo;
        $this->query = $query;

        if ($this->pdo->getAttribute( PDO::ATTR_DRIVER_NAME ) == 'mysql') {
            $this->pdo->setAttribute( PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false );
        }
    }

    /**
     * @param array $queryParameters
     */
    public function setQueryParameters( array $queryParameters )
    {
        $this->queryParameters = $queryParameters;
    }

    public function open()
    {
        $this->lineCount = 0;

        if (is_null( $this->reader )) {
            $this->reader = $this->pdo->prepare( $this->query, [ PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY ] );
            $this->reader->setFetchMode( PDO::FETCH_ASSOC );
        }

        return $this->reader->execute( $this->queryParameters );
    }

    public function close()
    {
        $this->reader          = null;
        $this->pdo             = null;
        $this->queryParameters = null;
        $this->currentRecord   = false;
    }

    public function current()
    {
        return $this->currentRecord;
    }

    public function next()
    {
        $this->currentRecord = $this->reader->fetch();
    }

    public function key()
    {
        return $this->lineCount;
    }

    public function valid()
    {
        $valid = !is_null( $this->reader ) && $this->currentRecord !== false;

        if ($valid) {
            $this->incrementLineCount();
        }

        return $valid;
    }

    public function rewind()
    {
        if (!is_null( $this->reader )) {
            $this->reader->closeCursor();
        }

        $this->currentRecord = $this->open() ? $this->reader->fetch() : null;
    }

    public function getInnerIterator()
    {
        return $this;
    }
}
