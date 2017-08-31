<?php

namespace RodrigoPedra\RecordProcessor;

use Iterator;
use IteratorIterator;
use RodrigoPedra\RecordProcessor\Contracts\Reader;
use RodrigoPedra\RecordProcessor\Contracts\RecordParser;
use RodrigoPedra\RecordProcessor\Traits\CountsRecords;

class Source extends IteratorIterator implements Iterator
{
    use CountsRecords;

    /** @var Reader */
    protected $reader;

    /** @var RecordParser */
    protected $recordParser;

    public function __construct( Reader $reader, RecordParser $recordParser )
    {
        parent::__construct( $reader );

        $this->reader       = $reader;
        $this->recordParser = $recordParser;
    }

    public function current()
    {
        $record = $this->recordParser->parseRecord( $this->reader, parent::current() );

        if ($record->valid()) {
            $this->incrementRecordCount();
        }

        return $record;
    }

    /**
     * @return  void
     */
    public function open()
    {
        $this->recordCount = 0;

        // implict opens on Reader::rewind()
        $this->reader->open();
    }

    /**
     * @return  void
     */
    public function close()
    {
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
}
