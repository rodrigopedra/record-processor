<?php

namespace RodrigoPedra\RecordProcessor\Traits\BuilderConcerns;

use InvalidArgumentException;
use RodrigoPedra\RecordProcessor\Contracts\RecordParser;
use RodrigoPedra\RecordProcessor\Records\Parsers\ArrayRecordParser;
use RodrigoPedra\RecordProcessor\Records\Parsers\CallbackRecordParser;
use RodrigoPedra\RecordProcessor\Source;

trait BuildsSource
{
    /** @var  RecordParser */
    protected $recordParser;

    /**
     * @param  RecordParser|callable $recordParser
     *
     * @return $this
     */
    public function usingParser( $recordParser )
    {
        if (is_callable( $recordParser )) {
            $this->recordParser = new CallbackRecordParser( $recordParser );

            return $this;
        }

        if (!$recordParser instanceof RecordParser) {
            throw new InvalidArgumentException( 'Invalid RecordFormatter' );
        }

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
