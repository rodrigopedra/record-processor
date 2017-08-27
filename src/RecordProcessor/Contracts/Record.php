<?php

namespace RodrigoPedra\RecordProcessor\Contracts;

interface Record
{
    /**
     * @param  string $field
     * @param  string $default
     *
     * @return mixed
     */
    public function getField( $field, $default = '' );

    /**
     * @return bool
     */
    public function valid();

    /**
     * @return array
     */
    public function toArray();

    /**
     * @return string|null
     */
    public function getKey();
}
