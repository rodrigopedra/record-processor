<?php

namespace RodrigoPedra\RecordProcessor\Readers;

use League\Csv\Reader as RawCsvReader;
use RodrigoPedra\RecordProcessor\Contracts\ConfigurableReader;
use RodrigoPedra\RecordProcessor\Helpers\Configurator;
use RodrigoPedra\RecordProcessor\Traits\CountsLines;
use RodrigoPedra\RecordProcessor\Traits\CsvControls;
use RodrigoPedra\RecordProcessor\Traits\ReaderInnerIterator;

class CSVReader implements ConfigurableReader
{
    use CsvControls, CountsLines, ReaderInnerIterator;

    /** @var string */
    protected $filepath = '';

    /** @var bool */
    protected $useFirstRowAsHeader = true;

    public function __construct( $filepath )
    {
        $this->filepath = $filepath;

        // default values
        $this->setDelimiter( ';' );
        $this->setEnclosure( '"' );
        $this->setEscape( '\\' );
        $this->setUseFirstRowAsHeader( true );
    }

    /**
     * @param bool $firstRowAsHeader
     */
    public function setUseFirstRowAsHeader( $firstRowAsHeader = true )
    {
        $this->useFirstRowAsHeader = $firstRowAsHeader;
    }

    public function open()
    {
        $this->lineCount = 0;

        /** @var RawCsvReader $csvReader */
        $csvReader = RawCsvReader::createFromPath( $this->filepath );

        $csvReader->setDelimiter( $this->getDelimiter() );
        $csvReader->setEnclosure( $this->getEnclosure() );
        $csvReader->setEscape( $this->getEscape() );

        if ($this->useFirstRowAsHeader) {
            $csvReader->setHeaderOffset( 0 );
        }

        $this->setInnerIterator( $csvReader->getRecords() );
    }

    /**
     * @return  void
     */
    public function close()
    {
        $this->setInnerIterator( null );
    }

    /**
     * @return array
     */
    public function getConfigurableMethods()
    {
        return [ 'setUseFirstRowAsHeader' ];
    }

    /**
     * @return Configurator
     */
    public function createConfigurator()
    {
        return new Configurator( $this );
    }
}
