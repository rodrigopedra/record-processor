<?php

namespace RodrigoPedra\RecordProcessor\Contracts;

use Illuminate\Contracts\Support\Arrayable;

interface Record extends Arrayable
{
    /**
     * @return mixed
     */
    public function key();

    /**
     * Gets and atribute from the record
     *
     * @param  string  $field
     * @return mixed
     */
    public function field(string $field);

    public function isValid(): bool;
}
