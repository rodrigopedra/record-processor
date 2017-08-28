<?php

namespace RodrigoPedra\RecordProcessor\Traits\BuilderConcerns;

use Illuminate\Support\Collection;
use Iterator;
use PDO;
use RodrigoPedra\RecordProcessor\Contracts\ConfigurableReader;
use RodrigoPedra\RecordProcessor\Contracts\Reader;
use RodrigoPedra\RecordProcessor\Readers\ArrayReader;
use RodrigoPedra\RecordProcessor\Readers\CollectionReader;
use RodrigoPedra\RecordProcessor\Readers\CSVFileReader;
use RodrigoPedra\RecordProcessor\Readers\ExcelFileReader;
use RodrigoPedra\RecordProcessor\Readers\IteratorReader;
use RodrigoPedra\RecordProcessor\Readers\PDOReader;
use RodrigoPedra\RecordProcessor\Readers\TextFileReader;

trait BuildsReaders
{
    /** @var  Reader */
    protected $reader;

    public function readFromArray( array $items )
    {
        $this->reader = new ArrayReader( $items );

        return $this;
    }

    public function readFromCollection( Collection $collection )
    {
        $this->reader = new CollectionReader( $collection );

        return $this;
    }

    public function readFromCSVFile( $fileName, callable $configurator = null )
    {
        $this->reader = new CSVFileReader( $fileName );

        $this->configureReader( $this->reader, $configurator );

        return $this;
    }

    public function readFromExcelFile( $fileName, callable $configurator = null )
    {
        $this->reader = new ExcelFileReader( $fileName );

        $this->configureReader( $this->reader, $configurator );

        return $this;
    }

    public function readFromIterator( Iterator $iterator )
    {
        $this->reader = new IteratorReader( $iterator );

        return $this;
    }

    public function readFromPDO( PDO $pdo, $query, array $parameters = [] )
    {
        $this->reader = new PDOReader( $pdo, $query );
        $this->reader->setQueryParameters( $parameters );

        return $this;
    }

    public function readFromTextFile( $fileName )
    {
        $this->reader = new TextFileReader( $fileName );

        return $this;
    }

    protected function configureReader( ConfigurableReader $reader, callable $configurator = null )
    {
        if (is_null( $configurator )) {
            return null;
        }

        $readerConfigurator = $reader->createConfigurator();

        call_user_func_array( $configurator, [ $readerConfigurator ] );

        return $readerConfigurator;
    }
}
