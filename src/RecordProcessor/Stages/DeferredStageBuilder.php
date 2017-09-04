<?php

namespace RodrigoPedra\RecordProcessor\Stages;

use Closure;
use RodrigoPedra\RecordProcessor\Contracts\ProcessorStage;

class DeferredStageBuilder implements ProcessorStage
{
    /** @var Closure */
    private $stageBuilder;

    public function __construct( Closure $stageBuilder )
    {

        $this->stageBuilder = $stageBuilder;
    }

    /**
     * @param array ...$parameters
     *
     * @return  ProcessorStage
     */
    public function build( ...$parameters )
    {
        return call_user_func_array( $this->stageBuilder, $parameters );
    }
}
