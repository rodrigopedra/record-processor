<?php

namespace RodrigoPedra\RecordProcessor\Traits;

use RodrigoPedra\RecordProcessor\Helpers\Configurator;

trait ConfiguresExcelReader
{
    /** @var  callable|null */
    protected $readerConfigurator = null;

    /**
     * @return callable|null
     */
    public function getReaderConfigurator()
    {
        if (is_null( $this->readerConfigurator )) {
            return null;
        }

        return function ( $reader ) {
            call_user_func_array( $this->readerConfigurator, [ $reader ] );
        };
    }

    /**
     * @param callable $readerConfigurator
     */
    public function setReaderConfigurator( callable $readerConfigurator )
    {
        $this->readerConfigurator = $readerConfigurator;
    }

    public function getConfigurableMethods()
    {
        return [ 'setReaderConfigurator' ];
    }

    public function createConfigurator()
    {
        /** @var \RodrigoPedra\RecordProcessor\Readers\ExcelFileReader $this */
        return new Configurator( $this );
    }
}
