<?php

namespace RodrigoPedra\RecordProcessor\Concerns;

use Psr\Log\LoggerInterface;

trait HasLogger
{
    protected ?LoggerInterface $logger;

    public function setLogger(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }
}
