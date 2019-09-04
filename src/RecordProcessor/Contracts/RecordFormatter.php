<?php

namespace RodrigoPedra\RecordProcessor\Contracts;

interface RecordFormatter
{
    /**
     * Encode Record objects content to writer format
     *
     * @param  Writer  $writer
     * @param  Record  $record
     * @return bool
     */
    public function formatRecord(Writer $writer, Record $record);
}
