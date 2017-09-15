<?php

namespace RodrigoPedra\RecordProcessor\Helpers\Writers;

use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\Writer;
use RodrigoPedra\RecordProcessor\Exceptions\InvalidAddonException;
use function RodrigoPedra\RecordProcessor\value_or_null;

class WriterAddon
{
    /** @var  array|callable */
    protected $addon;

    public function __construct( $addon )
    {
        if (!is_array( $addon ) && !is_callable( $addon )) {
            throw new InvalidAddonException;
        }

        $this->addon = $addon;
    }

    public function handle( Writer $writer, $recordCount, Record $record = null )
    {
        if (is_array( $this->addon )) {
            $writer->append( $this->addon );

            return;
        }

        $content = call_user_func_array( $this->addon, [ new WriterCallbackProxy( $writer, $recordCount, $record ) ] );
        $content = value_or_null( $content );

        if (is_null( $content )) {
            return;
        }

        $writer->append( $content );
    }
}
