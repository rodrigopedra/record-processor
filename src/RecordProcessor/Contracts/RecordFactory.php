<?php

namespace RodrigoPedra\RecordProcessor\Contracts;

interface RecordFactory
{
    /**
     * @param  array $fields
     *
     * @return Record
     */
    public function makeRecord( array $fields );
}
