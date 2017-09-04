<?php

namespace RodrigoPedra\RecordProcessor\Records\Formatter;

use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\RecordFormatter;
use RodrigoPedra\RecordProcessor\Contracts\Writer;

class CallbackRecordFormatter implements RecordFormatter
{
    /** @var callable */
    protected $callback;

    public function __construct( callable $callback )
    {
        $this->callback = $callback;
    }

    public function formatRecord( Writer $writer, Record $record )
    {
        $data = call_user_func_array( $this->callback, [ $record ] );

        if ($data === false) {
            return false;
        }

        $writer->append( $data );

        return true;
    }
}
