<?php

namespace RodrigoPedra\RecordProcessor\Records\Formatter;

use RodrigoPedra\RecordProcessor\Contracts\JsonRecord;
use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\RecordFormatter;
use RodrigoPedra\RecordProcessor\Contracts\Writer;
use RodrigoPedra\RecordProcessor\Writers\JSONFileWriter;
use RuntimeException;

class JsonRecordFormatter implements RecordFormatter
{
    /** @var  bool */
    protected $writesValidRecords = true;

    /** @var  int */
    protected $jsonEncodeOptions;

    public function __construct( $writesValidRecords = true, $jsonEncodeOptions = null )
    {
        $this->writesValidRecords = $writesValidRecords;

        $this->jsonEncodeOptions = is_int( $jsonEncodeOptions )
            ? $jsonEncodeOptions
            : JSONFileWriter::JSON_ENCODE_OPTIONS;
    }

    public function formatRecord( Writer $writer, Record $record )
    {
        if (!$record instanceof JsonRecord) {
            $className = get_class( $record );

            throw new RuntimeException( "'{$className}' should implement JsonRecord interface" );
        }

        if ($this->writesValidRecords xor $record->valid()) {
            return false;
        }

        $writer->append( $record->toJson( $this->jsonEncodeOptions ) ?: '' );

        return true;
    }
}
