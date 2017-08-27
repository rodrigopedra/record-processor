<?php

namespace RodrigoPedra\RecordProcessor\Writers;

use League\Csv\ByteSequence;
use League\Csv\Writer as RawCsvWriter;
use RodrigoPedra\RecordProcessor\Contracts\ConfigurableWriter;
use RodrigoPedra\RecordProcessor\Contracts\NewLines;
use RodrigoPedra\RecordProcessor\Helpers\WriterConfigurator;
use RodrigoPedra\RecordProcessor\Traits\CountsLines;
use RodrigoPedra\RecordProcessor\Traits\CsvControls;
use RodrigoPedra\RecordProcessor\Traits\NoOutput;

class CSVWriter implements ConfigurableWriter, ByteSequence, NewLines
{
    use CsvControls, CountsLines, NoOutput;

    const CUSTOM_DATA_TRAILLER = '<< <<';

    /** @var RawCsvWriter|null */
    protected $writer = null;

    /** @var string */
    protected $filepath = '';

    public function __construct( $filepath )
    {
        $this->filepath = $filepath;

        // defaults
        $this->setDelimiter( ';' );
        $this->setNewline( static::WINDOWS_NEWLINE );
        $this->setOutputBOM( static::BOM_UTF8 );
    }

    public function open()
    {
        $this->lineCount = 0;

        $this->writer = RawCsvWriter::createFromPath( $this->filepath, 'wb' );

        $this->writer->setOutputBOM( $this->getOutputBOM() );
        $this->writer->setDelimiter( $this->getDelimiter() );
        $this->writer->setEnclosure( $this->getEnclosure() );
        $this->writer->setNewline( $this->getNewline() );
        $this->writer->setEscape( $this->getEscape() );
    }

    public function close()
    {
        $this->writer = null;
    }

    /**
     * @param  array $content
     *
     * @return void
     */
    public function append( $content )
    {
        $this->writer->insertOne( $content );

        $this->incrementLineCount();
    }

    public function getConfigurableMethods()
    {
        return [
            'setOutputBOM',
            'setDelimiter',
            'setEnclosure',
            'setNewline',
            'setEscape',
        ];
    }

    public function createConfigurator()
    {
        return new WriterConfigurator( $this, true, true );
    }
}
