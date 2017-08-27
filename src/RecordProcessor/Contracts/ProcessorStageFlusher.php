<?php

namespace RodrigoPedra\RecordProcessor\Contracts;

use RodrigoPedra\RecordProcessor\Stages\TransferObjects\FlushPayload;

interface ProcessorStageFlusher extends ProcessorStage
{
    /**
     * @param  FlushPayload $payload
     *
     * @return mixed
     */
    public function flush( FlushPayload $payload );
}
