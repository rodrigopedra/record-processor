<?php

namespace RodrigoPedra\RecordProcessor\Examples\RecordObjects;

use RodrigoPedra\RecordProcessor\Contracts\TextRecord;
use RodrigoPedra\RecordProcessor\Records\SimpleRecord;

class ExampleRecord extends SimpleRecord implements TextRecord
{
    public function getKey()
    {
        return $this->get('name');
    }

    public function valid()
    {
        return filter_var($this->get('email'), FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * @return string
     */
    public function toText()
    {
        return implode('|', $this->toArray());
    }
}
