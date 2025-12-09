<?php

namespace RodrigoPedra\RecordProcessor\Concerns\Builder;

use RodrigoPedra\RecordProcessor\Configurators\Serializers\SerializerConfigurator;
use RodrigoPedra\RecordProcessor\Contracts\RecordSerializer;
use RodrigoPedra\RecordProcessor\Contracts\Serializer;
use RodrigoPedra\RecordProcessor\Contracts\Serializer as SerializerContract;
use RodrigoPedra\RecordProcessor\RecordSerializers\CallbackRecordSerializer;
use RodrigoPedra\RecordProcessor\Serializers\ArraySerializer;
use RodrigoPedra\RecordProcessor\Serializers\CollectionSerializer;
use RodrigoPedra\RecordProcessor\Serializers\CSVFileSerializer;
use RodrigoPedra\RecordProcessor\Serializers\EchoSerializer;
use RodrigoPedra\RecordProcessor\Serializers\ExcelFileSerializer;
use RodrigoPedra\RecordProcessor\Serializers\HTMLTableSerializer;
use RodrigoPedra\RecordProcessor\Serializers\JSONFileSerializer;
use RodrigoPedra\RecordProcessor\Serializers\LogSerializer;
use RodrigoPedra\RecordProcessor\Serializers\PDOBufferedSeriealizer;
use RodrigoPedra\RecordProcessor\Serializers\PDOSerializer;
use RodrigoPedra\RecordProcessor\Serializers\TextFileSerializer;
use RodrigoPedra\RecordProcessor\Stages\Writer;

trait BuildsSerializers
{
    protected ?RecordSerializer $recordSerializer = null;

    public function withRecordSerializer(RecordSerializer|callable $recordSerializer): static
    {
        if (\is_callable($recordSerializer)) {
            $recordSerializer = new CallbackRecordSerializer($recordSerializer);
        }

        $this->recordSerializer = $recordSerializer;

        return $this;
    }

    public function serializeToArray(?callable $configurator = null): static
    {
        $serializer = new ArraySerializer();

        $this->configureSerializer($serializer, $configurator);
        $this->addSerializer($serializer);

        return $this;
    }

    public function serializeToCollection(?callable $configurator = null): static
    {
        $serializer = new CollectionSerializer();

        $this->configureSerializer($serializer, $configurator);
        $this->addSerializer($serializer);

        return $this;
    }

    public function serializeToCSVFile(\SplFileObject|string|null $fileName = null, ?callable $configurator = null): static
    {
        $serializer = new CSVFileSerializer($fileName);

        $this->configureSerializer($serializer, $configurator);
        $this->addSerializer($serializer);

        return $this;
    }

    public function serializeToEcho(?callable $configurator = null): static
    {
        $serializer = new EchoSerializer();

        $this->configureSerializer($serializer, $configurator);
        $this->addSerializer($serializer);

        return $this;
    }

    public function serializeToExcelFile(\SplFileObject|string $fileName, ?callable $configurator = null): static
    {
        $serializer = new ExcelFileSerializer($fileName);

        $this->configureSerializer($serializer, $configurator);
        $this->addSerializer($serializer);

        return $this;
    }

    public function serializeToHTMLTable(?callable $configurator = null): static
    {
        $serializer = new HTMLTableSerializer();

        $this->configureSerializer($serializer, $configurator);
        $this->addSerializer($serializer);

        return $this;
    }

    public function serializeToJSONFile(\SplFileObject|string|null $fileName = null, ?callable $configurator = null): static
    {
        $serializer = new JSONFileSerializer($fileName);

        $this->configureSerializer($serializer, $configurator);
        $this->addSerializer($serializer);

        return $this;
    }

    public function serializeToLog(?callable $configurator = null): static
    {
        $serializer = new LogSerializer($this);

        $this->configureSerializer($serializer, $configurator);
        $this->addSerializer($serializer);

        return $this;
    }

    public function serializeToPDO(
        \PDO $pdo,
        string $tableName,
        array $columns,
        bool $buffered = true,
        ?callable $configurator = null,
    ): static {
        $serializer = $buffered
            ? new PDOBufferedSeriealizer($pdo, $tableName, $columns)
            : new PDOSerializer($pdo, $tableName, $columns);

        $this->configureSerializer($serializer, $configurator);
        $this->addSerializer($serializer);

        return $this;
    }

    public function serializeToTextFile(\SplFileObject|string|null $fileName = null, ?callable $configurator = null): static
    {
        $serializer = new TextFileSerializer($fileName);

        $this->configureSerializer($serializer, $configurator);
        $this->addSerializer($serializer);

        return $this;
    }

    protected function configureSerializer(Serializer $serializer, ?callable $callback = null): SerializerConfigurator
    {
        $configurator = $serializer->configurator();

        if (\is_callable($callback)) {
            \call_user_func($callback, $configurator);
        }

        return $configurator;
    }

    protected function addSerializer(SerializerContract $instance): static
    {
        $configurator = $instance->configurator();

        if (! \is_null($this->recordSerializer) && ! $configurator->hasRecordSerializer()) {
            $configurator->withRecordSerializer($this->recordSerializer);
        }

        $writer = new Writer($instance);
        $writer->withHeader($configurator->header());
        $writer->withTrailler($configurator->trailler());

        return $this->addStage($writer);
    }
}
