<?php

namespace RodrigoPedra\RecordProcessor\Examples\RecordObjects;

use RodrigoPedra\RecordProcessor\Records\KeyedRecord;

class ExampleRecord extends KeyedRecord
{
    public function isValid(): bool
    {
        return \filter_var($this->get('email'), \FILTER_VALIDATE_EMAIL) !== false;
    }

    public function toText(): string
    {
        return \implode('|', $this->toArray());
    }
}
