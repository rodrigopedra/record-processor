<?php

namespace RodrigoPedra\RecordProcessor\Records;

use RodrigoPedra\RecordProcessor\Contracts\JsonRecord;
use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\RecordAggregate;
use RodrigoPedra\RecordProcessor\Contracts\TextRecord;

final class NullRecord implements Record, JsonRecord, TextRecord, RecordAggregate
{
    private static ?NullRecord $instance = null;

    private function __construct() {}

    public function key(): null
    {
        return null;
    }

    public function field(string $field, $default = null): null
    {
        return null;
    }

    public function isValid(): bool
    {
        return false;
    }

    public function toArray(): array
    {
        return [];
    }

    public function toJson($options = 0): string
    {
        return 'null';
    }

    public function toText(): string
    {
        return '';
    }

    public function master(): NullRecord
    {
        return $this;
    }

    public function addRecord(Record $record): bool
    {
        return false;
    }

    public function records(): iterable
    {
        return [];
    }

    public function count(): int
    {
        return 0;
    }

    public static function get(): NullRecord
    {
        return self::$instance ??= new self();
    }
}
