<?php

namespace RodrigoPedra\RecordProcessor\Contracts;

use Illuminate\Contracts\Support\Arrayable;

interface Record extends Arrayable
{
    public function key(): mixed;

    public function field(string $field): mixed;

    public function isValid(): bool;
}
