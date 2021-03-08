<?php

namespace RodrigoPedra\RecordProcessor\Contracts;

use RodrigoPedra\RecordProcessor\Support\TransferObjects\FlushPayload;

interface ProcessorStageFlusher extends ProcessorStage
{
    public function flush(FlushPayload $payload, \Closure $next): ?FlushPayload;
}
