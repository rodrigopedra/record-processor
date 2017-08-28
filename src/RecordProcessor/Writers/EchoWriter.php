<?php

namespace RodrigoPedra\RecordProcessor\Writers;

use RodrigoPedra\RecordProcessor\Contracts\ConfigurableWriter;
use RodrigoPedra\RecordProcessor\Helpers\WriterConfigurator;
use RodrigoPedra\RecordProcessor\Traits\HasPrefix;
use RodrigoPedra\RecordProcessor\Traits\NoOutput;

class EchoWriter extends FileWriter implements ConfigurableWriter
{
    use HasPrefix, NoOutput;

    public function __construct()
    {
        parent::__construct( 'php://output' );
    }

    public function open()
    {
        $this->lineCount = 0;
    }

    /**
     * @param  string $content
     *
     * @return void
     */
    public function append( $content )
    {
        $prefix = $this->getPrefix();

        if (is_string( $prefix )) {
            $this->file->fwrite( $prefix . ': ' );
        }

        if (!is_string( $content )) {
            $content = var_export( $content, true );
        }

        $this->file->fwrite( $content );
        $this->file->fwrite( PHP_EOL );
        $this->file->fwrite( PHP_EOL );

        $this->incrementLineCount();
    }

    public function getConfigurableMethods()
    {
        return [ 'setPrefix' ];
    }

    public function createConfigurator()
    {
        return new WriterConfigurator( $this, true, true );
    }
}
