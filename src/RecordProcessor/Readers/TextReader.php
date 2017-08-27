<?php

namespace RodrigoPedra\RecordProcessor\Readers;

use RodrigoPedra\RecordProcessor\Contracts\NewLines;
use RodrigoPedra\RecordProcessor\Contracts\Reader;
use RodrigoPedra\RecordProcessor\Traits\CountsLines;
use RodrigoPedra\RecordProcessor\Traits\ReaderInnerIterator;
use SplFileObject;

/**
 * Class TextReader
 *
 * @property  \SplFileObject $iterator
 *
 * @package RodrigoPedra\Converters\Readers
 */
class TextReader implements Reader
{
    use CountsLines, ReaderInnerIterator {
        current as iteratorCurrent;
        valid as iteratorValid;
    }

    /** @var string */
    protected $filepath = '';

    public function __construct( $filepath )
    {
        $this->filepath = $filepath;
    }

    public function open()
    {
        $this->lineCount = 0;

        $reader = new SplFileObject( $this->filepath, 'r' );

        $this->setInnerIterator( $reader );
    }

    public function close()
    {
        $this->setInnerIterator( null );
    }

    public function current()
    {
        $content = $this->iteratorCurrent();

        return rtrim( $content, NewLines::WINDOWS_NEWLINE ); // removes line endings
    }

    public function valid()
    {
        return $this->iteratorValid() && !$this->iterator->eof();
    }
}
