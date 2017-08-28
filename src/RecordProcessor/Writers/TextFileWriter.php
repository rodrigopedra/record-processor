<?php

namespace RodrigoPedra\RecordProcessor\Writers;

use RodrigoPedra\RecordProcessor\Contracts\ConfigurableWriter;
use RodrigoPedra\RecordProcessor\Contracts\NewLines;
use RodrigoPedra\RecordProcessor\Helpers\WriterConfigurator;
use RuntimeException;

class TextFileWriter extends FileWriter implements ConfigurableWriter, NewLines
{
    /** @var string */
    protected $newLine = self::WINDOWS_NEWLINE;

    public function getNewLine()
    {
        return $this->newLine;
    }

    /**
     * @param  string $newLine
     */
    public function setNewLine( $newLine )
    {
        $this->newLine = $newLine;
    }

    public function append( $content )
    {
        if (!is_string( $content )) {
            throw new RuntimeException( 'content for TextWriter should be a string' );
        }

        $content = sprintf( '%s%s', $content, $this->getNewLine() );
        $this->file->fwrite( $content );

        $this->incrementLineCount( substr_count( $content, $this->getNewLine() ) );
    }

    public function getConfigurableMethods()
    {
        return [ 'setNewLine' ];
    }

    public function createConfigurator()
    {
        return new WriterConfigurator( $this, true, true );
    }
}
