<?php

namespace RodrigoPedra\RecordProcessor\Concerns;

trait NoOutput
{
    public function output()
    {
        return null;
    }
}
