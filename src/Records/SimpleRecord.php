<?php

namespace RodrigoPedra\RecordProcessor\Records;

use Illuminate\Support\Arr;
use Illuminate\Support\Fluent;
use RodrigoPedra\RecordProcessor\Contracts\JsonRecord;
use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\TextRecord;
use RodrigoPedra\RecordProcessor\Serializers\JSONFileSerializer;

class SimpleRecord extends Fluent implements Record, TextRecord, JsonRecord
{
    public function key(): mixed
    {
        return Arr::first($this->attributes);
    }

    public function field(string $field): mixed
    {
        return $this->get($field);
    }

    public function isValid(): bool
    {
        return \filled($this->key());
    }

    public function toText(): string
    {
        return $this->toJson(JSONFileSerializer::JSON_ENCODE_OPTIONS);
    }
}
