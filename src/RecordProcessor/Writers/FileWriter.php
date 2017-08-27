<?php

namespace RodrigoPedra\RecordProcessor\Writers;

use RodrigoPedra\RecordProcessor\Contracts\Writer;
use RodrigoPedra\RecordProcessor\Traits\CountsLines;
use SplFileInfo;

abstract class FileWriter extends SplFileInfo implements Writer
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
