<?php

namespace RodrigoPedra\RecordProcessor\Contracts;

interface RecordParser
{
    /**
     * Generates Record objects from raw data
     *
     * @param  Reader  $reader
     * @param  mixed  $rawContent
     * @return Record
     */
    public function parseRecord(Reader $reader, $rawContent);
}
