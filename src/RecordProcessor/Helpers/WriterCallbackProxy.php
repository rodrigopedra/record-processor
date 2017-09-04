<?php

namespace RodrigoPedra\RecordProcessor\Helpers;

use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\Writer;

class WriterCallbackProxy
{
    /** @var Writer */
    protected $writer;

    /** @var  int */
    protected $recordCount;

    /** @var Record */
    protected $firstRecord;

    public function __construct( Writer $writer, $recordCount, Record $firstRecord = null )
    {
        $this->writer      = $writer;
        $this->recordCount = $recordCount;
        $this->firstRecord = $firstRecord;
    }

    public function append( $content )
    {
        $this->writer->append( $content );
    }

    public function getLineCount()
    {
        return $this->writer->getLineCount();
    }

    public function getRecordCount()
    {
        return $this->recordCount;
    }

    public function getFirstRecord()
    {
        return $this->firstRecord;
    }
}
