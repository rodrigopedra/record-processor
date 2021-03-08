<?php

namespace RodrigoPedra\RecordProcessor\Contracts;

interface TextRecord extends Record
{
    public function toText(): string;
}
