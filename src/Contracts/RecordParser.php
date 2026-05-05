<?php

namespace RodrigoPedra\RecordProcessor\Contracts;

interface RecordParser
{
    /**
     * @return \RodrigoPedra\RecordProcessor\Contracts\Record|iterable<\RodrigoPedra\RecordProcessor\Contracts\Record>
     */
    public function parseRecords(Reader $reader, $rawContent): Record|iterable;
}
