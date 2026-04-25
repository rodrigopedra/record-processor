<?php

namespace RodrigoPedra\RecordProcessor\Contracts;

interface RecordParser
{
    public function parseRecords(Reader $reader, $rawContent): Record|iterable;
}
