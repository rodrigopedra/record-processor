<?php

namespace RodrigoPedra\RecordProcessor\Writers;

use RodrigoPedra\RecordProcessor\Contracts\ConfigurableWriter;
use RodrigoPedra\RecordProcessor\Contracts\NewLines;
use RodrigoPedra\RecordProcessor\Helpers\WriterConfigurator;
use RuntimeException;
use SplFileObject;

class TextFileWriter extends FileWriter implements ConfigurableWriter, NewLines
{
    /** @var SplFileObject */
    protected $writer = null;

    /** @var string */
    protected $newLine;

    public function __construct( $fileName )
    {
        parent::__construct( $fileName );

        // default values
        $this->setNewLine( static::WINDOWS_NEWLINE );
    }

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

    public function open()
    {
        $this->lineCount = 0;

        $this->writer = new SplFileObject( $this->getPathname(), 'wb' );
    }

    public function close()
    {
        $this->writer = null;
    }

    public function append( $content )
    {
        if (!is_string( $content )) {
            throw new RuntimeException( 'content for TextWriter should be a string' );
        }

        $content = sprintf( '%s%s', $content, $this->getNewLine() );

        $this->writer->fwrite( $content );

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
