<?php

namespace RodrigoPedra\RecordProcessor\Writers;

use RodrigoPedra\RecordProcessor\Contracts\ConfigurableWriter;
use RodrigoPedra\RecordProcessor\Contracts\NewLines;
use RodrigoPedra\RecordProcessor\Helpers\WriterConfigurator;
use function RodrigoPedra\RecordProcessor\value_or_null;

class JSONFileWriter extends FileWriter implements ConfigurableWriter, NewLines
{
    /** @var int */
    protected $jsonEncodeOptions = JSON_NUMERIC_CHECK | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;

    /**
     * @param int $jsonEncodeOptions
     */
    public function setJsonEncodeOptions( $jsonEncodeOptions )
    {
        $this->jsonEncodeOptions = $jsonEncodeOptions;
    }

    public function close()
    {
        $this->file->fwrite( ']' );
    }

    public function append( $content )
    {
        if (is_object( $content ) && method_exists( $content, 'toJson' )) {
            $content = $content->toJson();

            $this->write( $content );

            return;
        }

        if (is_object( $content ) && method_exists( $content, 'jsonSerialize' )) {
            $content = $content->jsonSerialize();
        }

        $content = value_or_null( $content );

        if (empty( $content )) {
            return;
        }

        $content = json_encode( $content, $this->jsonEncodeOptions );

        $this->write( $content );
    }

    protected function write( $content )
    {
        $prepend = $this->getLineCount() === 0 ? '[' : ',';
        $content = sprintf( '%s%s', $prepend, $content );

        $this->file->fwrite( $content );

        $this->incrementLineCount();
    }

    public function getConfigurableMethods()
    {
        return [ 'setJsonEncodeOptions' ];
    }

    public function createConfigurator()
    {
        return new WriterConfigurator( $this, false, false );
    }
}
