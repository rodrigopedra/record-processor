<?php

namespace RodrigoPedra\RecordProcessor\Concerns\Builder;

use Illuminate\Support\Collection;
use RodrigoPedra\RecordProcessor\Configurators\Readers\ReaderConfigurator;
use RodrigoPedra\RecordProcessor\Contracts\Reader;
use RodrigoPedra\RecordProcessor\Contracts\RecordParser;
use RodrigoPedra\RecordProcessor\Parser;
use RodrigoPedra\RecordProcessor\Reader\ArrayReader;
use RodrigoPedra\RecordProcessor\Reader\CollectionReader;
use RodrigoPedra\RecordProcessor\Reader\CSVFileReader;
use RodrigoPedra\RecordProcessor\Reader\ExcelFileReader;
use RodrigoPedra\RecordProcessor\Reader\IteratorReader;
use RodrigoPedra\RecordProcessor\Reader\PDOReader;
use RodrigoPedra\RecordProcessor\Reader\TextFileParser;
use RodrigoPedra\RecordProcessor\RecordParsers\CallbackRecordParser;

trait BuildsParser
{
    protected Reader $reader;
    protected ?RecordParser $recordParser = null;

    public function withRecordParser(RecordParser|callable $recordParser): static
    {
        if (\is_callable($recordParser)) {
            $recordParser = new CallbackRecordParser($recordParser);
        }

        $this->recordParser = $recordParser;

        return $this;
    }

    public function readFromArray(array $items, ?callable $configurator = null): static
    {
        $this->reader = new ArrayReader($items);

        $this->configureReader($this->reader, $configurator);

        return $this;
    }

    public function readFromCollection(Collection $collection, ?callable $configurator = null): static
    {
        $this->reader = new CollectionReader($collection);

        $this->configureReader($this->reader, $configurator);

        return $this;
    }

    public function readFromCSVFile(\SplFileObject|string $fileName, ?callable $configurator = null): static
    {
        $this->reader = new CSVFileReader($fileName);

        $this->configureReader($this->reader, $configurator);

        return $this;
    }

    public function readFromExcelFile(\SplFileObject|string $fileName, ?callable $configurator = null): static
    {
        $this->reader = new ExcelFileReader($fileName);

        $this->configureReader($this->reader, $configurator);

        return $this;
    }

    public function readFromIterator(\Iterator $iterator, ?callable $configurator = null): static
    {
        $this->reader = new IteratorReader($iterator);

        $this->configureReader($this->reader, $configurator);

        return $this;
    }

    public function readFromPDO(\PDO $pdo, $query, array $parameters = [], ?callable $configurator = null): static
    {
        $this->reader = new PDOReader($pdo, $query);
        $this->reader->withBindings($parameters);

        $this->configureReader($this->reader, $configurator);

        return $this;
    }

    public function readFromTextFile(\SplFileObject|string $fileName, ?callable $configurator = null): static
    {
        $this->reader = new TextFileParser($fileName);

        $this->configureReader($this->reader, $configurator);

        return $this;
    }

    protected function configureReader(Reader $reader, ?callable $callback = null): ReaderConfigurator
    {
        $configurator = $reader->configurator();

        if (\is_callable($callback)) {
            \call_user_func($callback, $configurator);
        }

        return $configurator;
    }

    protected function makeParser(): Parser
    {
        $configurator = $this->reader->configurator();

        if (! \is_null($this->recordParser) && ! $configurator->hasRecordParser()) {
            $configurator->withRecordParser($this->recordParser);
        }

        return new Parser($this->reader);
    }
}
