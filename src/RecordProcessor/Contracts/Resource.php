<?php

namespace RodrigoPedra\RecordProcessor\Contracts;

interface Resource
{
    /**
     * @return  void
     */
    public function open();

    /**
     * @return  void
     */
    public function close();
}
