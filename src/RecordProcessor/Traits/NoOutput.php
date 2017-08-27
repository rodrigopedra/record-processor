<?php

namespace RodrigoPedra\RecordProcessor\Traits;

trait NoOutput
{
    public function hasOutput()
    {
        return false;
    }

    public function output()
    {
        return null;
    }
}
