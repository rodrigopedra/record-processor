<?php

namespace RodrigoPedra\RecordProcessor\Readers;

use League\Csv\Reader as RawCsvReader;
use RodrigoPedra\RecordProcessor\Contracts\ConfigurableReader;
use RodrigoPedra\RecordProcessor\Helpers\Configurator;
use RodrigoPedra\RecordProcessor\Traits\CsvControls;

class CSVFileReader extends FileReader implements ConfigurableReader
{
    use CsvControls;

    /** @var bool */
    protected $useFirstRowAsHeader = true;

    public function __construct( $file )
    {
        parent::__construct( $file );

        // default values
        $this->setDelimiter( ';' );
        $this->setEnclosure( '"' );
        $this->setEscape( '\\' );
        $this->useFirstRowAsHeader( true );
    }

    /**
     * @param bool $firstRowAsHeader
     */
    public function useFirstRowAsHeader( $firstRowAsHeader = true )
    {
        $this->useFirstRowAsHeader = $firstRowAsHeader;
    }

    public function open()
    {
        parent::open();

        /** @var RawCsvReader $csvReader */
        $csvReader = RawCsvReader::createFromFileObject( $this->file );

        $csvReader->setDelimiter( $this->getDelimiter() );
        $csvReader->setEnclosure( $this->getEnclosure() );
        $csvReader->setEscape( $this->getEscape() );

        if ($this->useFirstRowAsHeader) {
            $csvReader->setHeaderOffset( 0 );
        }

        $this->setInnerIterator( $csvReader->getRecords() );
    }

    /**
     * @return array
     */
    public function getConfigurableMethods()
    {
        return [ 'useFirstRowAsHeader' ];
    }

    /**
     * @return Configurator
     */
    public function createConfigurator()
    {
        return new Configurator( $this );
    }
}
