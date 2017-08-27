<?php

namespace RodrigoPedra\RecordProcessor\Examples;

use RodrigoPedra\RecordProcessor\Contracts\Reader;
use RodrigoPedra\RecordProcessor\Contracts\RecordParser;
use RodrigoPedra\RecordProcessor\Records\InvalidRecord;

class ExampleRecordParser implements RecordParser
{
    public function parseRecord( Reader $reader, $rawContent )
    {
        if (is_string( $rawContent )) {
            $rawContent = explode( '|', $rawContent );
        }

        $values = array_wrap( $rawContent );

        if (count( $values ) < 2) {
            return new InvalidRecord;
        }

        return new ExampleRecord( [
            'name'  => reset( $values ),
            'email' => end( $values ),
        ] );
    }
}
