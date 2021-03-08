<?php

namespace RodrigoPedra\RecordProcessor\Contracts;

interface RecordParser
{
    public function parseRecord(Reader $reader, $rawContent): Record;
}
