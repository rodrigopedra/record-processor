<?php

namespace RodrigoPedra\RecordProcessor;

if (!function_exists( 'value_or_null' )) {
    /**
     * Return the default value of the given value or null if the value is empty.
     *
     * @param  mixed $value
     *
     * @return mixed
     */
    function value_or_null( $value )
    {
        $value = value( $value );

        if (is_object( $value )) {
            return $value;
        }

        if (is_array( $value )) {
            return empty( $value ) ? null : $value;
        }

        $value = trim( $value );

        if (empty( $value ) || !$value) {
            return null;
        }

        return $value;
    }
}

if (!function_exists( 'is_associative_array' )) {
    /**
     * Check if the array is an associative array
     *
     * @param  array $value
     *
     * @return bool
     */
    function is_associative_array( $value )
    {
        return is_array( $value ) && array_diff_key( $value, array_keys( array_keys( $value ) ) );
    }
}
