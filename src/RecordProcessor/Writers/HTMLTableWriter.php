<?php

namespace RodrigoPedra\RecordProcessor\Writers;

use League\Csv\HTMLConverter;
use RodrigoPedra\RecordProcessor\Contracts\ConfigurableWriter;
use RodrigoPedra\RecordProcessor\Contracts\NewLines;
use RodrigoPedra\RecordProcessor\Helpers\FileInfo;
use RodrigoPedra\RecordProcessor\Helpers\WriterConfigurator;
use RodrigoPedra\RecordProcessor\Traits\CountsLines;
use SplFileObject;
use function RodrigoPedra\RecordProcessor\value_or_null;

class HTMLTableWriter implements ConfigurableWriter
{
    use CountsLines;

    /** @var HTMLConverter|null */
    protected $writer = null;

    /** @var array */
    protected $records = [];

    /** @var string|\SplFileObject */
    protected $output = '';

    /** @var string */
    protected $tableClassAttribute = '';

    /** @var string */
    protected $tableIdAttribute = '';

    /** @var SplFileObject|null */
    protected $file = null;

    /**
     * @param  string $fileName
     */
    public function writeOutputToFile( $fileName )
    {
        $this->file = FileInfo::createWritableFileObject( $fileName, 'wb' );
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

        if (!is_null( $this->file )) {
            $this->file->fwrite( $this->output );
            $this->file->fwrite( NewLines::UNIX_NEWLINE );

            $this->output = FileInfo::createReadableFileObject( $this->file );
        }

        $this->writer  = null;
        $this->records = [];
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
