<?php

namespace RodrigoPedra\RecordProcessor\BuilderConcerns;

use PDO;
use RodrigoPedra\RecordProcessor\Contracts\ConfigurableWriter;
use RodrigoPedra\RecordProcessor\Writers\ArrayWriter;
use RodrigoPedra\RecordProcessor\Writers\CollectionWriter;
use RodrigoPedra\RecordProcessor\Writers\CSVFileWriter;
use RodrigoPedra\RecordProcessor\Writers\EchoWriter;
use RodrigoPedra\RecordProcessor\Writers\ExcelFileWriter;
use RodrigoPedra\RecordProcessor\Writers\HTMLTableWriter;
use RodrigoPedra\RecordProcessor\Writers\JSONFileWriter;
use RodrigoPedra\RecordProcessor\Writers\LogWriter;
use RodrigoPedra\RecordProcessor\Writers\PDOBufferedWriter;
use RodrigoPedra\RecordProcessor\Writers\PDOWriter;
use RodrigoPedra\RecordProcessor\Writers\TextFileWriter;

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

    public function writeToCSVFile( $fileName, callable $configurator = null )
    {
        $writer = new CSVFileWriter( $fileName );

        $this->addCompiler( $writer, $this->configureWriter( $writer, $configurator ) );

        return $this;
    }

    public function writeToEcho( callable $configurator = null )
    {
        $writer = new EchoWriter;

        $this->addCompiler( $writer, $this->configureWriter( $writer, $configurator ) );

        return $this;
    }

    public function writeToExcelFile( $fileName, callable $configurator = null )
    {
        $writer = new ExcelFileWriter( $fileName );

        $this->addCompiler( $writer, $this->configureWriter( $writer, $configurator ) );

        return $this;
    }

    public function writeToHTMLTable( callable $configurator = null )
    {
        $writer = new HTMLTableWriter;

        $this->addCompiler( $writer, $this->configureWriter( $writer, $configurator ) );

        return $this;
    }

    public function writeToJSONFile( $fileName, callable $configurator = null )
    {
        $writer = new JSONFileWriter( $fileName );

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

    public function writeToTextFile( $fileName, callable $configurator = null )
    {
        $writer = new TextFileWriter( $fileName );

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
