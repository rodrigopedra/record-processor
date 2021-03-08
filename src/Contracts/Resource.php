<?php

namespace RodrigoPedra\RecordProcessor\Contracts;

interface Resource
{
    public function open();

    public function close();
}
