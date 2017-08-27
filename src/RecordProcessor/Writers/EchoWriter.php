<?php

namespace RodrigoPedra\RecordProcessor\Writers;

use RodrigoPedra\RecordProcessor\Contracts\ConfigurableWriter;
use RodrigoPedra\RecordProcessor\Helpers\WriterConfigurator;
use RodrigoPedra\RecordProcessor\Traits\CountsLines;
use RodrigoPedra\RecordProcessor\Traits\HasPrefix;
use RodrigoPedra\RecordProcessor\Traits\NoOutput;

class EchoWriter implements ConfigurableWriter
{
    use CountsLines, HasPrefix, NoOutput;

    public function open()
    {
        $this->lineCount = 0;
    }

    public function close()
    {
        //
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
            echo $prefix, ':', PHP_EOL;
        }

        if (!is_string( $content )) {
            $content = var_export( $content, true );
        }

        echo $content, PHP_EOL;
        echo PHP_EOL;

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
