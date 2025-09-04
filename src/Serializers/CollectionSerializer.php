<?php

namespace RodrigoPedra\RecordProcessor\Serializers;

use Illuminate\Support\Collection;
use RodrigoPedra\RecordProcessor\Configurators\Serializers\SerializerConfigurator;
use RodrigoPedra\RecordProcessor\Contracts\Serializer;
use RodrigoPedra\RecordProcessor\RecordSerializers\ArrayRecordSerializer;

class CollectionSerializer implements Serializer
{
    protected readonly SerializerConfigurator $configurator;

    protected Collection $collection;

    public function __construct()
    {
        $this->configurator = new SerializerConfigurator($this, false, false);
        $this->open();
    }

    public function open(): void
    {
        $this->collection = new Collection();
    }

    public function close(): void {}

    public function append($content): void
    {
        $this->collection->push($content);
    }

    public function lineCount(): int
    {
        return $this->collection->count();
    }

    public function output(): Collection
    {
        return $this->collection;
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
