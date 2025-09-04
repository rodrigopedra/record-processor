<?php

namespace RodrigoPedra\RecordProcessor\Serializers;

use RodrigoPedra\RecordProcessor\Configurators\Serializers\SerializerConfigurator;
use RodrigoPedra\RecordProcessor\Contracts\Serializer;
use RodrigoPedra\RecordProcessor\RecordSerializers\ArrayRecordSerializer;

class ArraySerializer implements Serializer
{
    protected readonly SerializerConfigurator $configurator;

    protected array $items = [];

    public function __construct()
    {
        $this->configurator = new SerializerConfigurator($this, false, false);
    }

    public function open(): void
    {
        $this->items = [];
    }

    public function close(): void {}

    public function append($content): void
    {
        $this->items[] = $content;
    }

    public function lineCount(): int
    {
        return \count($this->items);
    }

    public function output(): array
    {
        return $this->items;
    }

    public function configurator(): SerializerConfigurator
    {
        return $this->configurator;
    }

    public function defaultRecordSerializer(): ArrayRecordSerializer
    {
        return new ArrayRecordSerializer();
    }
}
