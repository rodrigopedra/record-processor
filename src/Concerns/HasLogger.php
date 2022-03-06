<?php

namespace RodrigoPedra\RecordProcessor\Concerns;

use Psr\Log\LoggerInterface;

trait HasLogger
{
    protected ?LoggerInterface $logger = null;

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
