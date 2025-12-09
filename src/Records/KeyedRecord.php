<?php

namespace RodrigoPedra\RecordProcessor\Records;

use Illuminate\Support\Fluent;
use RodrigoPedra\RecordProcessor\Contracts\JsonRecord;
use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\TextRecord;
use RodrigoPedra\RecordProcessor\Serializers\JSONFileSerializer;

class KeyedRecord extends Fluent implements Record, TextRecord, JsonRecord
{
    public function __construct(
        protected readonly ?string $key,
        array $attributes = [],
    ) {
        parent::__construct($attributes);
    }

    public function key(): ?string
    {
        return $this->key;
    }

    public function field(string $field, $default = null)
    {
        return $this->get($field, $default);
    }

    public function isValid(): bool
    {
        return \filled($this->key);
    }

    public function toText(): string
    {
        return $this->toJson(JSONFileSerializer::JSON_ENCODE_OPTIONS);
    }
}
