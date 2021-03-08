<?php

namespace RodrigoPedra\RecordProcessor\Contracts;

interface RecordFactory
{
    public function makeRecord(array $fields): Record;
}
