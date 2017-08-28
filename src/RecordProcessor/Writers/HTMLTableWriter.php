<?php

namespace RodrigoPedra\RecordProcessor\Writers;

use League\Csv\HTMLConverter;
use RodrigoPedra\RecordProcessor\Contracts\ConfigurableWriter;
use RodrigoPedra\RecordProcessor\Contracts\NewLines;
use RodrigoPedra\RecordProcessor\Helpers\FileInfo;
use RodrigoPedra\RecordProcessor\Helpers\WriterConfigurator;
use RodrigoPedra\RecordProcessor\Traits\CountsLines;
use function RodrigoPedra\RecordProcessor\value_or_null;

class HTMLTableWriter implements ConfigurableWriter
{
    use CountsLines;

    /** @var HTMLConverter|null */
    protected $writer = null;

    /** @var array */
    protected $records = [];

    /** @var string */
    protected $output = '';

    /** @var string */
    protected $tableClassAttribute = '';

    /** @var string */
    protected $tableIdAttribute = '';

    /** @var  FileInfo */
    protected $fileInfo;

    /**
     * @param  string $fileName
     */
    public function writeOutputToFile( $fileName )
    {
        $this->fileInfo = new FileInfo( $fileName );
    }

    /**
     * @param  string $tableClassAttribute
     */
    public function setTableClassAttribute( $tableClassAttribute )
    {
        $this->tableClassAttribute = value_or_null( $tableClassAttribute ) ?: '';
    }

    /**
     * @param  string $tableIdAttribute
     */
    public function setTableIdAttribute( $tableIdAttribute )
    {
        $this->tableIdAttribute = value_or_null( $tableIdAttribute ) ?: '';
    }

    public function open()
    {
        $this->lineCount = 0;
        $this->output    = '';
        $this->records   = [];

        $this->writer = ( new HTMLConverter )
            // should be chained, ->table() returns a cloned HTMLConverter instance
            ->table( $this->tableClassAttribute, $this->tableIdAttribute );
    }

    public function close()
    {
        $this->output = $this->writer->convert( $this->records );

        if (!is_null( $this->fileInfo )) {
            $outputFile = $this->fileInfo->openFile( 'wb' );
            $outputFile->fwrite( $this->output );
            $outputFile->fwrite( NewLines::UNIX_NEWLINE );

            $this->output = $this->fileInfo;
        }

        $this->fileInfo = null;
        $this->writer   = null;
        $this->records  = [];
    }

    public function append( $content )
    {
        array_push( $this->records, array_wrap( $content ) );

        $this->incrementLineCount();
    }

    public function output()
    {
        return $this->output;
    }

    public function getConfigurableMethods()
    {
        return [
            'setTableClassAttribute',
            'setTableIdAttribute',
            'writeOutputToFile',
        ];
    }

    public function createConfigurator()
    {
        return new WriterConfigurator( $this, true, true );
    }
}
