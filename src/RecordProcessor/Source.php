<?php

namespace RodrigoPedra\RecordProcessor;

use Generator;
use Iterator;
use RodrigoPedra\RecordProcessor\Contracts\Reader;
use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\RecordParser;
use RodrigoPedra\RecordProcessor\Traits\CountsLines;
use RodrigoPedra\RecordProcessor\Traits\CountsRecords;
use RuntimeException;

class Source implements Iterator
{
    use CountsRecords, CountsLines;

    /** @var Reader */
    protected $reader;

    /** @var RecordParser */
    protected $recordParser;

    /** @var Generator|null */
    protected $recordGenerator = null;

    public function __construct( Reader $reader, RecordParser $recordParser )
    {
        $this->reader       = $reader;
        $this->recordParser = $recordParser;
    }

    /**
     * @return  void
     */
    public function open()
    {
        $this->recordCount     = 0;
        $this->recordGenerator = null;

        $this->reader->open();
    }

    /**
     * @return  void
     */
    public function close()
    {
        $this->recordGenerator = null;

        $this->reader->close();
    }

    /**
     * @return  Reader
     */
    public function getReader()
    {
        return $this->reader;
    }

    /**
     * @return  int
     */
    public function getLineCount()
    {
        return $this->reader->getLineCount();
    }

    protected static $count = 0;

    public function current()
    {
        $result = is_null( $this->recordGenerator )
            ? $this->recordParser->parseRecord( $this->reader, $this->reader->current() )
            : $this->recordGenerator;

        if ($result instanceof Record) {
            $this->recordGenerator = null;

            return $this->verifyRecord( $result );
        }

        if ($result instanceof Generator) {
            if ($result->valid()) {
                $result = $this->handleGenerator( $result );

                return $this->verifyRecord( $result );
            }

            $this->recordGenerator = null;

            return null;
        }

        if (is_null( $result )) {
            $this->recordGenerator = null;

            return null;
        }

        throw new RuntimeException( 'Record parser should return or generate a Record instance' );
    }

    public function next()
    {
        $this->incrementLineCount();

        if (is_null( $this->recordGenerator )) {
            $this->reader->next();
        }
    }

    public function key()
    {
        return $this->getLineCount();
    }

    public function valid()
    {
        return $this->reader->valid();
    }

    public function rewind()
    {
        $this->lineCount = 0;

        $this->reader->rewind();
        $this->recordGenerator = null;
    }

    protected function verifyRecord( Record $record )
    {
        if ($record->valid()) {
            $this->incrementRecordCount();
        }

        return $record;
    }

    protected function handleGenerator( Generator $generator )
    {
        $record = $generator->current();

        if (!$record instanceof Record) {
            throw new RuntimeException( 'Record parser should return or generate a Record instance' );
        }

        $this->recordGenerator = $generator;
        $this->recordGenerator->next();

        return $record;
    }
}
