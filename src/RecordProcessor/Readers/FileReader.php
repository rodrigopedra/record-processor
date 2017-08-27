<?php

namespace RodrigoPedra\RecordProcessor\Readers;

use RodrigoPedra\RecordProcessor\Contracts\Reader;
use RodrigoPedra\RecordProcessor\Traits\CountsLines;
use SplFileInfo;

abstract class FileReader extends SplFileInfo implements Reader
{
    use CountsLines;
}
