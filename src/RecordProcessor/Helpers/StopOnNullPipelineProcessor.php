<?php

namespace RodrigoPedra\RecordProcessor\Helpers;

use League\Pipeline\ProcessorInterface;

class StopOnNullPipelineProcessor implements ProcessorInterface
{
    /**
     * @param  array $stages
     * @param  mixed $payload
     *
     * @return mixed
     */
    public function process( array $stages, $payload )
    {
        foreach ($stages as $stage) {
            if (is_null( $payload )) {
                return null;
            }

            $payload = call_user_func_array( $stage, [ $payload ] );
        }

        return $payload;
    }
}
