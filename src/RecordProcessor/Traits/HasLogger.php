<?php

namespace RodrigoPedra\RecordProcessor\Traits;

use Psr\Log\LoggerInterface;

trait HasLogger
{
    /** @var  LoggerInterface */
    protected $logger;

    /**
     * @param  LoggerInterface  $logger
     * @return void
     */
    public function setLogger(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }
}
