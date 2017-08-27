<?php

namespace RodrigoPedra\RecordProcessor\Readers;

use RodrigoPedra\RecordProcessor\Contracts\NewLines;
use RodrigoPedra\RecordProcessor\Traits\ReaderInnerIterator;
use SplFileObject;

/**
 * Class TextReader
 *
 * @property  \SplFileObject $iterator
 *
 * @package RodrigoPedra\Converters\Readers
 */
class TextReader extends FileReader
{
    use ReaderInnerIterator {
        current as iteratorCurrent;
        valid as iteratorValid;
    }

    public function open()
    {
        $this->lineCount = 0;

        $reader = new SplFileObject( $this->getRealPath(), 'r' );

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
