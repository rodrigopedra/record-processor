<?php

namespace RodrigoPedra\RecordProcessor\Traits;

trait HasOutput
{
    public function hasOutput()
    {
        return true;
    }

    abstract public function output();
}
