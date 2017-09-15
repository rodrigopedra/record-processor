<?php

namespace RodrigoPedra\RecordProcessor\Traits;

use RodrigoPedra\RecordProcessor\Helpers\Writers\WriterConfigurator;

trait ConfiguresExcelWriter
{
    /** @var  callable|null */
    protected $workbookConfigurator = null;

    /** @var  callable|null */
    protected $worksheetConfigurator = null;

    /**
     * @return callable|null
     */
    public function getWorkbookConfigurator()
    {
        if (is_null( $this->workbookConfigurator )) {
            return null;
        }

        return function ( $excel ) {
            call_user_func_array( $this->workbookConfigurator, [ $excel ] );
        };
    }

    /**
     * @param callable $workbookConfigurator
     */
    public function setWorkbookConfigurator( callable $workbookConfigurator )
    {
        $this->workbookConfigurator = $workbookConfigurator;
    }

    /**
     * @return callable
     */
    public function getWorksheetConfigurator()
    {
        if (is_null( $this->worksheetConfigurator )) {
            return null;
        }

        return function ( $sheet ) {
            call_user_func_array( $this->worksheetConfigurator, [ $sheet ] );
        };
    }

    /**
     * @param callable $worksheetConfigurator
     */
    public function setWorksheetConfigurator( callable $worksheetConfigurator )
    {
        $this->worksheetConfigurator = $worksheetConfigurator;
    }

    public function getConfigurableMethods()
    {
        return [
            'setWorkbookConfigurator',
            'setWorksheetConfigurator',
        ];
    }

    public function createConfigurator()
    {
        /** @var \RodrigoPedra\RecordProcessor\Writers\ExcelFileWriter $this */
        return new WriterConfigurator( $this, true, true );
    }
}
