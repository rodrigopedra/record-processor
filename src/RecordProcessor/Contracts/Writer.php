<?php

namespace RodrigoPedra\RecordProcessor\Contracts;

interface Writer extends Resource
{
    /**
     * @param  mixed $content
     *
     * @return void
     */
    public function append( $content );

    /**
     * @return  int
     */
    public function getLineCount();

    /**
     * @return mixed
     */
    public function output();
}
