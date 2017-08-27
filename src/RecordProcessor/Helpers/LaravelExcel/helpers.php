<?php

/*
 * maatwebsite/excel does not work without these functions
 */

if (class_exists( 'Illuminate\Foundation\Application', true )) {
    // if we are in a Laravel application, exits
    return;
}

if (!function_exists( 'config' )) {
    /**
     * Get / set the specified configuration value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param  array|string $key
     * @param  mixed        $default
     *
     * @return mixed
     */
    function config( $key = null, $default = null )
    {
        if (is_null( $key )) {
            return null;
        }

        return $default;
    }
}

if (!function_exists( 'base_path' )) {
    /**
     * Get the path to the base of the install.
     *
     * @param  string $path
     *
     * @return string
     */
    function base_path( $path = '' )
    {
        $path = trim( $path, DIRECTORY_SEPARATOR );
        $path = implode( DIRECTORY_SEPARATOR, [ __DIR__, '..', '..', '..', '..', $path ] );

        return rtrim( $path, DIRECTORY_SEPARATOR );
    }
}

if (!function_exists( 'storage_path' )) {
    /**
     * Get the path to the storage folder.
     *
     * @param  string $path
     *
     * @return string
     */
    function storage_path( $path = '' )
    {
        return sys_get_temp_dir();
    }
}

if (!function_exists( 'app' )) {
    /**
     * Get the available container instance.
     *
     * @param  string $abstract
     * @param  array  $parameters
     *
     * @return mixed
     */
    function app( $abstract = null, array $parameters = [] )
    {
        if ($abstract === 'excel') {
            return \RodrigoPedra\RecordProcessor\Helpers\LaravelExcel\Factory::getExcel();
        }

        return null;
    }
}
