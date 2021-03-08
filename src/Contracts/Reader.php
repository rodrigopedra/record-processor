<?php

namespace RodrigoPedra\RecordProcessor\Contracts;

use RodrigoPedra\RecordProcessor\Configurators\Readers\ReaderConfigurator;

interface Reader extends Resource, \OuterIterator
{
    public function lineCount(): int;

    public function configurator(): ReaderConfigurator;

    public function defaultRecordParser(): RecordParser;
}
