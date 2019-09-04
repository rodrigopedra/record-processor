<?php

namespace RodrigoPedra\RecordProcessor\Records;

use Illuminate\Support\Fluent;
use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\JsonRecord;
use RodrigoPedra\RecordProcessor\Contracts\TextRecord;
use RodrigoPedra\RecordProcessor\Writers\JSONFileWriter;

class SimpleRecord extends Fluent implements Record, TextRecord, JsonRecord
{
    public function get($field, $default = '')
    {
        return parent::get($field, $default);
    }

    public function set($field, $value)
    {
        $this->offsetSet($field, $value);
    }

    public function valid()
    {
        return strlen($this->getKey()) > 0;
    }

    public function getKey()
    {
        return reset($this->attributes) ?: '';
    }

    public function toText()
    {
        return $this->toJson(JSONFileWriter::JSON_ENCODE_OPTIONS);
    }
}
