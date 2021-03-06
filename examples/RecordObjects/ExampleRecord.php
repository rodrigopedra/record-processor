<?php

namespace RodrigoPedra\RecordProcessor\Examples\RecordObjects;

use RodrigoPedra\RecordProcessor\Contracts\TextRecord;
use RodrigoPedra\RecordProcessor\Records\SimpleRecord;

class ExampleRecord extends SimpleRecord implements TextRecord
{
    public function isValid(): bool
    {
        return \filter_var($this->get('email'), FILTER_VALIDATE_EMAIL) !== false;
    }

    public function toText(): string
    {
        return \implode('|', $this->toArray());
    }
}
