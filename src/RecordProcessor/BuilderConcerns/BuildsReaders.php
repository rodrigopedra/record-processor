<?php

namespace RodrigoPedra\RecordProcessor\BuilderConcerns;

use Illuminate\Support\Collection;
use Iterator;
use PDO;
use RodrigoPedra\RecordProcessor\Contracts\Reader;
use RodrigoPedra\RecordProcessor\Readers\ArrayReader;
use RodrigoPedra\RecordProcessor\Readers\CollectionReader;
use RodrigoPedra\RecordProcessor\Readers\CSVReader;
use RodrigoPedra\RecordProcessor\Readers\ExcelReader;
use RodrigoPedra\RecordProcessor\Readers\IteratorReader;
use RodrigoPedra\RecordProcessor\Readers\PDOReader;
use RodrigoPedra\RecordProcessor\Readers\TextReader;

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

    public function readFromCSV( $filepath, callable $configurator = null )
    {
        $this->reader = new CSVReader( $filepath );

        if (is_callable( $configurator )) {
            $configuratorObject = $this->reader->createConfigurator();

            call_user_func_array( $configurator, [ $configuratorObject ] );
        }

        return $this;
    }

    public function readFromExcel( $filepath )
    {
        $this->reader = new ExcelReader( $filepath );

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

    public function readFromText( $filepath )
    {
        $this->reader = new TextReader( $filepath );

        return $this;
    }
}
