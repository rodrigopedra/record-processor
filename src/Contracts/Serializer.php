<?php

namespace RodrigoPedra\RecordProcessor\Contracts;

use RodrigoPedra\RecordProcessor\Configurators\Serializers\SerializerConfigurator;

interface Serializer extends Resource
{
    public function append($content): void;

    public function lineCount(): int;

    public function output();

    public function configurator(): SerializerConfigurator;

    public function defaultRecordSerializer(): RecordSerializer;
}
