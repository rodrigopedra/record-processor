#!/usr/bin/env php
<?php

require __DIR__ . '/../../vendor/autoload.php';

use RodrigoPedra\RecordProcessor\Examples\RecordObjects\ExampleRecord;
use RodrigoPedra\RecordProcessor\ProcessorBuilder;

$items = [
    [ 'Rodrigo', 'rodrigo@example.com', 'rodrigo@example.org' ],
    [ 'Noemi', 'noemi@example.com' ],
    [ 'Bruno', 'bruno@example.com', 'bruno@example.org' ],
];

$processor = ( new ProcessorBuilder )
    ->readFromArray( $items )
    ->usingParser( function ( $rawContent ) {
        $name = array_get( $rawContent, 0 );

        foreach (range( 1, 2 ) as $index) {
            $email = array_get( $rawContent, $index ) ?: false;

            if ($email === false) {
                return;
            }

            yield new ExampleRecord( compact( 'name', 'email' ) );
        }
    } )
    ->writeToHTMLTable()
    ->build();

$output = $processor->process();

echo $output->getOutput(), PHP_EOL;

exit;
