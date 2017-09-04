<?php

namespace RodrigoPedra\RecordProcessor\Contracts;

interface RecordFactory
{
    /**
     * @param  array $fields
     *
     * @return Record
     */
    public static function makeRecord( array $fields );
}
