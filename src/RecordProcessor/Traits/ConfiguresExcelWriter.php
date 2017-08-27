<?php

namespace RodrigoPedra\RecordProcessor\Traits;

use RodrigoPedra\RecordProcessor\Helpers\WriterConfigurator;

trait ConfiguresExcelWriter
{
    /** @var  callable */
    protected $workbookConfigurator;

    /** @var  callable */
    protected $worksheetConfigurator;

    /**
     * @return callable
     */
    public function getWorkbookConfigurator()
    {
        return function ( $excel ) {
            if (is_null( $this->workbookConfigurator )) {
                return;
            }

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
        return function ( $sheet ) {
            if (is_null( $this->worksheetConfigurator )) {
                return;
            }

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

    public function getConfigurableSetters()
    {
        return [
            'setWorkbookConfigurator',
            'setWorksheetConfigurator',
        ];
    }

    public function createConfigurator()
    {
        /** @var \RodrigoPedra\RecordProcessor\Writers\ExcelWriter $this */
        return new WriterConfigurator( $this, true, true );
    }
}
