<?php

namespace RodrigoPedra\RecordProcessor\Writers;

use RodrigoPedra\RecordProcessor\Contracts\Writer;
use RodrigoPedra\RecordProcessor\Helpers\FileInfo;
use RodrigoPedra\RecordProcessor\Traits\CountsLines;

abstract class FileWriter extends FileInfo implements Writer
{
    use CountsLines;

    /**
     * @return mixed
     */
    public function output()
    {
        return $this->getFileInfo();
    }
}
