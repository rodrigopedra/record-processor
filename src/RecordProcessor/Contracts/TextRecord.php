<?php

namespace RodrigoPedra\RecordProcessor\Contracts;

interface TextRecord extends Record
{
    /**
     * @return string
     */
    public function toText();
}
