<?php

namespace RodrigoPedra\RecordProcessor;

use Traversable;
use IteratorIterator;
use RodrigoPedra\RecordProcessor\Contracts\Reader;
use RodrigoPedra\RecordProcessor\Contracts\RecordParser;

class Source extends IteratorIterator
{
    /** @var Reader */
    protected $reader;

    /** @var RecordParser */
    protected $recordParser;

    public function __construct(Reader $reader, RecordParser $recordParser)
    {
        parent::__construct($reader);

        $this->reader = $reader;
        $this->recordParser = $recordParser;
    }

    public function current()
    {
        $result = $this->recordParser->parseRecord($this->reader, parent::current());

        if ($result instanceof Traversable) {
            return $result;
        }

        return is_array($result) ? $result : [$result];
    }

    /**
     * @return  void
     */
    public function open()
    {
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
     * @return  int
     */
    public function getLineCount()
    {
        return $this->reader->getLineCount();
    }
}
