<?php

namespace RodrigoPedra\RecordProcessor\Contracts;

interface Resource
{
    public function open(): void;

    public function close(): void;
}
