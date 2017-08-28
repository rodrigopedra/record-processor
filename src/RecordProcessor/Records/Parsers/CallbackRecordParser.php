<?php

namespace RodrigoPedra\RecordProcessor\Records\Parsers;

use RodrigoPedra\RecordProcessor\Contracts\Reader;
use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\RecordParser;
use RuntimeException;

class CallbackRecordParser implements RecordParser
{
    /** @var callable */
    protected $callback;

    public function __construct( callable $callback )
    {
        $this->callback = $callback;
    }

    public function parseRecord( Reader $reader, $rawContent )
    {
        $record = call_user_func_array( $this->callback, [ $rawContent ] );

        if (!$record instanceof Record) {
            throw new RuntimeException( 'RecordParser should return a Record implementation' );
        }

        return $record;
    }
}
