<?php

namespace RodrigoPedra\RecordProcessor\BuilderConcerns;

use RodrigoPedra\RecordProcessor\Contracts\RecordParser;
use RodrigoPedra\RecordProcessor\Records\Parsers\ArrayRecordParser;
use RodrigoPedra\RecordProcessor\Source;

trait BuildsSource
{
    /** @var  RecordParser */
    protected $recordParser;

    public function usingParser( RecordParser $recordParser )
    {
        $this->recordParser = $recordParser;

        return $this;
    }

    protected function makeRecordParser()
    {
        if (is_null( $this->recordParser )) {
            return new ArrayRecordParser;
        }

        return $this->recordParser;
    }

    protected function makeSource()
    {
        $recordParser = $this->makeRecordParser();

        return new Source( $this->reader, $recordParser );
    }
}
