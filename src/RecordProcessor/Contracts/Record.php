<?php

namespace RodrigoPedra\RecordProcessor\Contracts;

use Illuminate\Contracts\Support\Arrayable;

interface Record extends Arrayable
{
    /**
     * @param  string  $field
     * @param  string|null  $default
     * @return mixed
     */
    public function get($field, $default = '');

    /**
     * @param  string  $field
     * @param  string|null  $value
     * @return mixed
     */
    public function set($field, $value);

    /**
     * @return string|null
     */
    public function getKey();

    /**
     * @return bool
     */
    public function valid();
}
