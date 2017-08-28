<?php

namespace RodrigoPedra\RecordProcessor\Stages\TransferObjects;

use RodrigoPedra\RecordProcessor\Contracts\Record;

class FlushPayload
{
    /** @var Record|null */
    protected $record = null;

    /** @var int */
    protected $lineCount = 0;

    /** @var int */
    protected $recordCount = 0;

    /** @var  mixed */
    protected $output = null;

    /** @var  string */
    protected $writerClassName = null;

    /**
     * @return bool
     */
    public function hasRecord()
    {
        return !is_null( $this->record );
    }

    /**
     * @return Record|null
     */
    public function getRecord()
    {
        return $this->record;
    }

    /**
     * @param Record|null $record
     */
    public function setRecord( $record )
    {
        $this->record = $record;
    }

    /**
     * @return int
     */
    public function getLineCount()
    {
        return $this->lineCount;
    }

    /**
     * @param int $lineCount
     */
    public function setLineCount( $lineCount )
    {
        $this->lineCount = $lineCount;
    }

    /**
     * @return int
     */
    public function getRecordCount()
    {
        return $this->recordCount;
    }

    /**
     * @param int $recordCount
     */
    public function setRecordCount( $recordCount )
    {
        $this->recordCount = $recordCount;
    }

    /**
     * @return mixed
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @param mixed $output
     */
    public function setOutput( $output )
    {
        $this->output = $output;
    }

    /**
     * @return string
     */
    public function getWriterClassName()
    {
        return $this->writerClassName;
    }

    /**
     * @param string $writerClassName
     */
    public function setWriterClassName( $writerClassName )
    {
        $this->writerClassName = $writerClassName;
    }
}
