<?php

namespace RodrigoPedra\RecordProcessor\Writers;

use RodrigoPedra\RecordProcessor\Contracts\ConfigurableWriter;
use RodrigoPedra\RecordProcessor\Contracts\NewLines;
use RodrigoPedra\RecordProcessor\Helpers\WriterConfigurator;
use RodrigoPedra\RecordProcessor\Traits\CountsLines;
use RodrigoPedra\RecordProcessor\Traits\NoOutput;
use RuntimeException;
use SplFileObject;

class JSONWriter implements ConfigurableWriter, NewLines
{
    use CountsLines, NoOutput;

    /** @var SplFileObject */
    protected $writer = null;

    /** @var string */
    protected $filepath = '';

    /** @var int */
    protected $jsonEncodeOptions;

    public function __construct( $filepath )
    {
        $this->filepath = $filepath;

        // default values
        $this->setJsonEncodeOptions( JSON_NUMERIC_CHECK | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT );
    }

    /**
     * @param int $jsonEncodeOptions
     */
    public function setJsonEncodeOptions( $jsonEncodeOptions )
    {
        $this->jsonEncodeOptions = $jsonEncodeOptions;
    }

    public function open()
    {
        $this->lineCount = 0;

        $this->writer = new SplFileObject( $this->filepath, 'wb' );
    }

    public function close()
    {
        $this->writer->fwrite( ']' );
        $this->writer = null;
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

        if (!is_array( $content )) {
            throw new RuntimeException( 'content for JSONWriter should be an array' );
        }

        if (count( $content ) === 0) {
            return;
        }

        $prepend = $this->getLineCount() === 0 ? '[' : ',';

        $content = json_encode( $content, $this->jsonEncodeOptions );
        $content = sprintf( '%s%s', $prepend, $content );

        $this->write( $content );
    }

    protected function write( $content )
    {
        $this->writer->fwrite( $content );

        $this->incrementLineCount();
    }

    public function getConfigurableSetters()
    {
        return [ 'setJsonEncodeOptions' ];
    }

    public function createConfigurator()
    {
        return new WriterConfigurator( $this, false, false );
    }
}
