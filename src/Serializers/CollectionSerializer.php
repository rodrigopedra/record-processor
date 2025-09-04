<?php

namespace RodrigoPedra\RecordProcessor\Serializers;

use Illuminate\Support\Collection;
use RodrigoPedra\RecordProcessor\Configurators\Serializers\SerializerConfigurator;
use RodrigoPedra\RecordProcessor\Contracts\Serializer;
use RodrigoPedra\RecordProcessor\RecordSerializers\ArrayRecordSerializer;

class CollectionSerializer implements Serializer
{
    protected readonly SerializerConfigurator $configurator;

    protected ?Collection $collection = null;

    public function __construct()
    {
        $this->configurator = new SerializerConfigurator($this, false, false);
    }

    public function open(): void
    {
        $this->collection = new Collection();
    }

    public function close(): void
    {
        $this->collection = null;
    }

    public function append($content): void
    {
        if (\is_null($this->collection)) {
            $this->open();
        }

        $this->collection->push($content);
    }

    public function lineCount(): int
    {
        return $this->collection?->count() ?? 0;
    }

    public function output(): Collection
    {
        return $this->collection ?? new Collection();
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
