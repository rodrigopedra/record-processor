<?php

namespace RodrigoPedra\RecordProcessor\Records;

use Illuminate\Support\Fluent;
use RodrigoPedra\RecordProcessor\Contracts\JsonRecord;
use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\TextRecord;
use RodrigoPedra\RecordProcessor\Serializers\JSONFileSerializer;

class SimpleRecord extends Fluent implements Record, TextRecord, JsonRecord
{
    public function __construct(
        protected ?string $key,
        array $attributes = [],
    ) {
        parent::__construct($attributes);
    }

    public function key(): ?string
    {
        return $this->key;
    }

    public function field(string $field): mixed
    {
        return $this->get($field);
    }

    public function isValid(): bool
    {
        if (\is_null($this->key)) {
            return false;
        }

        return \mb_strlen($this->key) > 0;
    }

    public function toText(): string
    {
        return $this->toJson(JSONFileSerializer::JSON_ENCODE_OPTIONS);
    }
}
