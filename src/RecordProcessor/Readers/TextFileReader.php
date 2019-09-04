<?php

namespace RodrigoPedra\RecordProcessor\Readers;

use RodrigoPedra\RecordProcessor\Contracts\NewLines;

/**
 * Class TextReader
 *
 * @property  \SplFileObject $iterator
 * @package RodrigoPedra\Converters\Readers
 */
class TextFileReader extends FileReader
{
    public function open()
    {
        parent::open();

        $this->setInnerIterator($this->file);
    }

    public function current()
    {
        $content = $this->iteratorCurrent();

        return rtrim($content, NewLines::WINDOWS_NEWLINE); // removes line endings
    }

    public function valid()
    {
        return $this->iteratorValid() && ! $this->iterator->eof();
    }
}
