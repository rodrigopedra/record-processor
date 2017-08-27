<?php

namespace RodrigoPedra\RecordProcessor\BuilderConcerns;

use PDO;
use RodrigoPedra\RecordProcessor\Contracts\ConfigurableWriter;
use RodrigoPedra\RecordProcessor\Writers\ArrayWriter;
use RodrigoPedra\RecordProcessor\Writers\CollectionWriter;
use RodrigoPedra\RecordProcessor\Writers\CSVWriter;
use RodrigoPedra\RecordProcessor\Writers\EchoWriter;
use RodrigoPedra\RecordProcessor\Writers\ExcelWriter;
use RodrigoPedra\RecordProcessor\Writers\HTMLTableWriter;
use RodrigoPedra\RecordProcessor\Writers\JSONWriter;
use RodrigoPedra\RecordProcessor\Writers\LogWriter;
use RodrigoPedra\RecordProcessor\Writers\PDOBufferedWriter;
use RodrigoPedra\RecordProcessor\Writers\PDOWriter;
use RodrigoPedra\RecordProcessor\Writers\TextWriter;

trait BuildsWriters
{
    public function writeToArray()
    {
        $writer = new ArrayWriter;

        $this->addCompiler( $writer );

        return $this;
    }

    public function writeToCollection()
    {
        $writer = new CollectionWriter;

        $this->addCompiler( $writer );

        return $this;
    }

    public function writeToCSV( $filepath, callable $configurator = null )
    {
        $writer = new CSVWriter( $filepath );

        $this->addCompiler( $writer, $this->configureWriter( $writer, $configurator ) );

        return $this;
    }

    public function writeToEcho( callable $configurator = null )
    {
        $writer = new EchoWriter;

        $this->addCompiler( $writer, $this->configureWriter( $writer, $configurator ) );

        return $this;
    }

    public function writeToExcel( $filepath, callable $configurator = null )
    {
        $writer = new ExcelWriter( $filepath );

        $this->addCompiler( $writer, $this->configureWriter( $writer, $configurator ) );

        return $this;
    }

    public function writeToHTMLTable( callable $configurator = null )
    {
        $writer = new HTMLTableWriter;

        $this->addCompiler( $writer, $this->configureWriter( $writer, $configurator ) );

        return $this;
    }

    public function writeToJSON( $filepath, callable $configurator = null )
    {
        $writer = new JSONWriter( $filepath );

        $this->addCompiler( $writer, $this->configureWriter( $writer, $configurator ) );

        return $this;
    }

    public function writeToLog( callable $configurator = null )
    {
        $writer = new LogWriter( $this->getLogger() );

        $this->addCompiler( $writer, $this->configureWriter( $writer, $configurator ) );

        return $this;
    }

    public function writeToPDO(
        PDO $pdo,
        $tableName,
        array $columns,
        $buffered = true,
        callable $configurator = null
    ) {
        $writer = $buffered === true
            ? new PDOBufferedWriter( $pdo, $tableName, $columns )
            : new PDOWriter( $pdo, $tableName, $columns );

        return $this->addCompiler( $writer, $this->configureWriter( $writer, $configurator ) );
    }

    public function writeToText( $filepath, callable $configurator = null )
    {
        $writer = new TextWriter( $filepath );

        $this->addCompiler( $writer, $this->configureWriter( $writer, $configurator ) );

        return $this;
    }

    protected function configureWriter( ConfigurableWriter $writer, callable $configurator = null )
    {
        if (is_null( $configurator )) {
            return null;
        }

        $writerConfigurator = $writer->createConfigurator();

        call_user_func_array( $configurator, [ $writerConfigurator ] );

        return $writerConfigurator;
    }
}
