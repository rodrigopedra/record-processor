<?php

namespace RodrigoPedra\RecordProcessor\Helpers;

use RodrigoPedra\RecordProcessor\Contracts\Writer;

class WriterCallbackProxy
{
    /** @var Writer */
    protected $writer;

    /** @var  int */
    protected $recordCount;

    public function __construct( Writer $writer, $recordCount )
    {
        $this->writer      = $writer;
        $this->recordCount = $recordCount;
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
}
