<?php

namespace RodrigoPedra\RecordProcessor\Helpers\LaravelExcel;

use Illuminate\Contracts\Bus\Dispatcher;

/*
 * maatwebsite/excel LaravelExcelReader requests a Dispatcher implementation in its constructor
 */

class NullDispatcher implements Dispatcher
{
    public function dispatch( $command )
    {
        return null;
    }

    public function dispatchNow( $command, $handler = null )
    {
        return null;
    }

    public function pipeThrough( array $pipes )
    {
        return $this;
    }
}
